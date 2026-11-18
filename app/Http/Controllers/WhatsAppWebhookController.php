<?php

namespace App\Http\Controllers;

use App\Models\WaMessage;
use App\Services\WhatsAppChatService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

/**
 * Webhook WhatsApp Cloud API (Meta).
 *
 * GET  /whatsapp/webhook — verifikasi alamat oleh Meta (hub.challenge).
 * POST /whatsapp/webhook — pemberitahuan pesan masuk. Wajib menjawab 200
 * dengan cepat; pemrosesan balasan (AI bisa lambat) dijalankan setelah
 * respons terkirim lewat terminating callback.
 */
class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode', $request->query('hub.mode'));
        $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        $expected = WhatsAppService::verifyToken();
        if ($mode === 'subscribe' && $expected !== '' && hash_equals($expected, (string) $token)) {
            return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Token verifikasi salah', 403);
    }

    public function receive(Request $request, WhatsAppChatService $chat)
    {
        $raw = $request->getContent();
        if (! WhatsAppService::signatureValid($raw, $request->header('X-Hub-Signature-256'))) {
            return response('Tanda tangan tidak valid', 401);
        }

        $payload = $request->json()->all();
        $jobs = [];

        foreach ((array) data_get($payload, 'entry', []) as $entry) {
            foreach ((array) data_get($entry, 'changes', []) as $change) {
                $value = (array) data_get($change, 'value', []);
                $contacts = [];
                foreach ((array) data_get($value, 'contacts', []) as $c) {
                    $contacts[(string) data_get($c, 'wa_id')] = data_get($c, 'profile.name');
                }
                foreach ((array) data_get($value, 'messages', []) as $msg) {
                    $job = $this->storeIncoming($msg, $contacts);
                    if ($job) {
                        $jobs[] = $job;
                    }
                }
            }
        }

        if ($jobs) {
            app()->terminating(function () use ($chat, $jobs) {
                foreach ($jobs as [$waMessage, $isText]) {
                    try {
                        $chat->handleIncoming($waMessage, $isText);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            });
        }

        return response()->json(['ok' => true]);
    }

    /** Simpan satu pesan masuk (dedupe memakai id pesan Meta). */
    private function storeIncoming(array $msg, array $contacts): ?array
    {
        $id = (string) data_get($msg, 'id', '');
        $from = (string) data_get($msg, 'from', '');
        if ($from === '') {
            return null;
        }
        if ($id !== '' && WaMessage::where('wa_message_id', $id)->exists()) {
            return null; // Meta mengirim ulang webhook yang sama — abaikan.
        }

        $type = (string) data_get($msg, 'type', '');
        $isText = $type === 'text';
        $body = $isText ? (string) data_get($msg, 'text.body', '') : '';

        $waMessage = WaMessage::create([
            'wa_message_id' => $id !== '' ? $id : null,
            'direction' => 'in',
            'wa_id' => $from,
            'contact_name' => $contacts[$from] ?? null,
            'body' => $body,
            'status' => 'received',
            'is_ai' => false,
        ]);

        return [$waMessage, $isText];
    }
}
