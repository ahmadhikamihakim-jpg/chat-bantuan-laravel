<?php

namespace App\Services;

use App\Models\ChatAgent;
use App\Models\ChatMenu;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * Otak AI untuk widget chat. Dua penyedia yang bisa diganti dari panel admin:
 *  - ollama : model AI lokal berjalan di komputer sendiri (Ollama, tanpa API key, tanpa internet)
 *  - gemini : Google Gemini API (kunci dibaca dari GEMINI_API_KEY di file .env)
 *
 * AI hanya menjawab dari Basis Pengetahuan (pengaturan ai_knowledge) ditambah
 * jawaban menu & daftar tim yang sudah ada di panel admin. Bila layanan AI
 * tidak terjangkau / gagal, ask() mengembalikan null dan widget memakai jalur
 * lama (kata kunci + fallback + WhatsApp).
 */
class ChatAiService
{
    public static function enabled(): bool
    {
        return Setting::get('chat_enabled', '1') === '1'
            && Setting::get('ai_enabled', '0') === '1';
    }

    public static function provider(): string
    {
        return Setting::get('ai_provider', 'ollama') === 'gemini' ? 'gemini' : 'ollama';
    }

    public static function model(): string
    {
        if (self::provider() === 'gemini') {
            return Setting::get('ai_model_gemini', '') ?: 'gemini-2.5-flash';
        }

        return Setting::get('ai_model_ollama', '') ?: 'qwen2.5:3b';
    }

    /** Susun instruksi untuk AI: jati diri + aturan main + bahan pengetahuan. */
    public function systemPrompt(): string
    {
        $bot = Setting::get('chat_bot_name', 'Lumi');
        $site = Setting::get('site_name', 'Website Contoh');

        $parts = [];
        $parts[] = "Kamu adalah {$bot}, asisten chat di website {$site}. Jawab selalu dalam Bahasa Indonesia yang sopan dan ramah.";
        $parts[] = 'ATURAN: (1) Jawab HANYA dari BASIS PENGETAHUAN di bawah; jangan mengarang harga, janji, atau fakta di luar bahan itu. '
            .'(2) Jawaban singkat dan jelas, paling banyak 4 kalimat, tanpa format markdown (tanpa **, tanpa daftar bintang). '
            .'(3) Bila jawaban tidak ada di basis pengetahuan, katakan dengan sopan bahwa kamu belum punya informasinya dan sarankan menekan tombol "Bicara dengan Tim" untuk dibantu lewat WhatsApp. '
            .'(4) Jangan mengaku sebagai manusia.';

        $knowledge = trim((string) Setting::get('ai_knowledge', ''));
        if ($knowledge !== '') {
            $parts[] = "BASIS PENGETAHUAN:\n".$knowledge;
        }

        $menuText = [];
        foreach (ChatMenu::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get() as $m) {
            if ($m->action === 'answer' && $m->answer) {
                $menuText[] = "- {$m->label}: {$m->answer}";
            }
        }
        if ($menuText) {
            $parts[] = "JAWABAN RESMI PER TOPIK:\n".implode("\n", $menuText);
        }

        $agents = ChatAgent::where('is_active', true)->orderBy('sort_order')->pluck('name')->all();
        if ($agents) {
            $parts[] = 'Tim yang bisa dihubungi lewat WhatsApp (tombol "Bicara dengan Tim"): '.implode(', ', $agents).'.';
        }

        return implode("\n\n", $parts);
    }

    /**
     * Kirim satu pertanyaan ke penyedia AI.
     * $history: daftar giliran sebelumnya [['role' => 'user'|'model', 'text' => '...'], ...]
     * Mengembalikan teks jawaban, atau null bila gagal (widget lalu memakai jalur lama).
     */
    public function ask(string $message, array $history = []): ?string
    {
        $history = $this->cleanHistory($history);

        try {
            $answer = self::provider() === 'gemini'
                ? $this->askGemini($message, $history)
                : $this->askOllama($message, $history);
        } catch (\Throwable $e) {
            return null;
        }

        $answer = trim((string) $answer);

        return $answer === '' ? null : mb_substr($answer, 0, 2000);
    }

    private function cleanHistory(array $history): array
    {
        $out = [];
        foreach (array_slice($history, -8) as $h) {
            $role = ($h['role'] ?? '') === 'model' ? 'model' : 'user';
            $text = trim((string) ($h['text'] ?? ''));
            if ($text !== '') {
                $out[] = ['role' => $role, 'text' => mb_substr($text, 0, 500)];
            }
        }

        return $out;
    }

    private function askOllama(string $message, array $history): ?string
    {
        $base = rtrim((string) (Setting::get('ai_ollama_url', '') ?: 'http://127.0.0.1:11434'), '/');

        $messages = [['role' => 'system', 'content' => $this->systemPrompt()]];
        foreach ($history as $h) {
            $messages[] = ['role' => $h['role'] === 'model' ? 'assistant' : 'user', 'content' => $h['text']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        // Model lokal di CPU bisa lambat: beri waktu sampai 120 detik.
        $res = Http::timeout(120)->connectTimeout(5)->post($base.'/api/chat', [
            'model' => self::model(),
            'stream' => false,
            'messages' => $messages,
            'options' => ['temperature' => 0.2, 'num_predict' => 220],
        ]);

        if (! $res->successful()) {
            return null;
        }

        return $res->json('message.content');
    }

    private function askGemini(string $message, array $history): ?string
    {
        $key = (string) config('services.gemini.key');
        if ($key === '') {
            return null;
        }

        $contents = [];
        foreach ($history as $h) {
            $contents[] = ['role' => $h['role'], 'parts' => [['text' => $h['text']]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.self::model().':generateContent';
        $res = Http::timeout(30)->withHeaders(['x-goog-api-key' => $key])->post($url, [
            'system_instruction' => ['parts' => [['text' => $this->systemPrompt()]]],
            'contents' => $contents,
            'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 350],
        ]);

        if (! $res->successful()) {
            return null;
        }

        $texts = [];
        foreach ((array) $res->json('candidates.0.content.parts', []) as $part) {
            if (! empty($part['text'])) {
                $texts[] = $part['text'];
            }
        }

        return $texts ? implode('', $texts) : null;
    }
}
