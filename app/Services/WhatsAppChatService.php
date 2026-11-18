<?php

namespace App\Services;

use App\Models\ChatAgent;
use App\Models\ChatLog;
use App\Models\ChatMenu;
use App\Models\Setting;
use App\Models\WaMessage;

/**
 * Mengurus satu pesan WhatsApp yang masuk: susun jawaban (AI bila aktif,
 * kalau tidak / bila AI gagal pakai pencocokan kata kunci menu, terakhir
 * teks fallback), lalu kirim balik lewat Cloud API dan catat semuanya.
 *
 * Dipanggil SETELAH webhook menjawab 200 ke Meta (lewat terminating
 * callback), supaya Meta tidak kehabisan waktu menunggu AI lokal menjawab.
 */
class WhatsAppChatService
{
    public function __construct(
        private ChatAiService $ai,
        private WhatsAppService $wa,
    ) {}

    public function handleIncoming(WaMessage $incoming, bool $isText = true): void
    {
        if (! WhatsAppService::aktif()) {
            $incoming->update(['status' => 'ignored']);

            return;
        }

        if ($incoming->wa_message_id) {
            $this->wa->markRead($incoming->wa_message_id);
        }

        if (! $isText) {
            $notice = 'Maaf, saat ini saya hanya bisa membaca pesan teks. Silakan ketik pertanyaan Anda, nanti saya bantu jawab.';
            $this->reply($incoming, $notice, matched: false, isAi: false);

            return;
        }

        $question = trim($incoming->body);
        $answer = null;
        $isAi = false;
        $matched = false;

        // 1) AI (bila Mode AI aktif di Pengaturan) — otak yang sama dengan widget web.
        if (ChatAiService::enabled()) {
            $answer = $this->ai->ask($question, $this->historyFor($incoming));
            if ($answer !== null) {
                $isAi = true;
                $matched = true;
            }
        }

        // 2) Kata kunci menu (jalur yang sama seperti widget sebelum ada AI).
        if ($answer === null) {
            $answer = $this->keywordAnswer($question);
            if ($answer !== null) {
                $matched = true;
            }
        }

        // 3) Fallback — pelanggan tetap menerima balasan.
        if ($answer === null) {
            $answer = (string) Setting::get('chat_fallback', 'Maaf, aku belum paham pertanyaan itu. Silakan tanya dengan kata lain, atau tunggu tim kami membalas.');
        }

        $this->reply($incoming, $answer, $matched, $isAi);
    }

    /** Riwayat percakapan nomor ini sebelum pesan $incoming (maks 8 giliran). */
    private function historyFor(WaMessage $incoming): array
    {
        $rows = WaMessage::where('wa_id', $incoming->wa_id)
            ->where('id', '<', $incoming->id)
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->reverse();

        $history = [];
        foreach ($rows as $r) {
            $history[] = [
                'role' => $r->direction === 'out' ? 'model' : 'user',
                'text' => $r->body,
            ];
        }

        return $history;
    }

    /** Pencocokan kata kunci sederhana meniru widget: kata kunci menu (dipisah koma) dicari di dalam pesan. */
    private function keywordAnswer(string $question): ?string
    {
        $q = mb_strtolower($question);
        $menus = ChatMenu::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        foreach ($menus as $m) {
            $keywords = array_filter(array_map('trim', explode(',', mb_strtolower((string) $m->keywords))));
            foreach ($keywords as $kw) {
                if ($kw !== '' && mb_strlen($kw) >= 3 && str_contains($q, $kw)) {
                    return $this->menuText($m);
                }
            }
        }

        return null;
    }

    private function menuText(ChatMenu $m): ?string
    {
        if ($m->action === 'answer' && $m->answer) {
            return $m->answer;
        }

        if ($m->action === 'agents') {
            $agents = ChatAgent::where('is_active', true)->orderBy('sort_order')->get();
            if ($agents->isEmpty()) {
                return null;
            }
            $lines = ['Anda bisa menghubungi tim kami:'];
            foreach ($agents as $a) {
                $lines[] = "- {$a->name} ({$a->label}): https://wa.me/{$a->wa_number}";
            }

            return implode("\n", $lines);
        }

        if ($m->action === 'link' && $m->url) {
            return "Silakan buka: {$m->url}";
        }

        return null;
    }

    private function reply(WaMessage $incoming, string $answer, bool $matched, bool $isAi): void
    {
        $sentId = $this->wa->sendText($incoming->wa_id, $answer);

        WaMessage::create([
            'wa_message_id' => $sentId,
            'direction' => 'out',
            'wa_id' => $incoming->wa_id,
            'contact_name' => $incoming->contact_name,
            'body' => $answer,
            'status' => $sentId ? 'sent' : 'failed',
            'is_ai' => $isAi,
        ]);

        $incoming->update(['status' => $sentId ? 'answered' : 'failed']);

        ChatLog::create([
            'type' => 'wa',
            'content' => $incoming->body !== '' ? $incoming->body : '[pesan bukan teks]',
            'matched' => $matched,
        ]);
    }
}
