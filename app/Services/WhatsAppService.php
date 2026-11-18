<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * Klien WhatsApp Cloud API (resmi dari Meta).
 *
 * Kredensial dibaca dari file .env (JANGAN diisi lewat chat / panel admin):
 *   WHATSAPP_ACCESS_TOKEN    token akses aplikasi Meta
 *   WHATSAPP_PHONE_NUMBER_ID id nomor pengirim (bukan nomor teleponnya)
 *   WHATSAPP_VERIFY_TOKEN    kata rahasia verifikasi webhook (bebas, samakan di dasbor Meta)
 *   WHATSAPP_APP_SECRET      app secret (opsional, untuk memeriksa tanda tangan webhook)
 *   WHATSAPP_GRAPH_VERSION   versi Graph API (bawaan v22.0)
 */
class WhatsAppService
{
    /** WhatsApp aktif bila saklar admin menyala DAN token + phone_number_id terisi. */
    public static function aktif(): bool
    {
        return Setting::get('chat_enabled', '1') === '1'
            && Setting::get('wa_enabled', '0') === '1'
            && (string) config('services.whatsapp.token') !== ''
            && (string) config('services.whatsapp.phone_number_id') !== '';
    }

    public static function verifyToken(): string
    {
        return (string) config('services.whatsapp.verify_token');
    }

    /** Periksa tanda tangan X-Hub-Signature-256 dari Meta (bila app secret diisi). */
    public static function signatureValid(string $rawBody, ?string $header): bool
    {
        $secret = (string) config('services.whatsapp.app_secret');
        if ($secret === '') {
            return true; // tidak dikonfigurasi -> lewati (token webhook tetap wajib untuk GET)
        }
        if (! $header || ! str_starts_with($header, 'sha256=')) {
            return false;
        }

        return hash_equals('sha256='.hash_hmac('sha256', $rawBody, $secret), $header);
    }

    private function url(string $path): string
    {
        $version = (string) (config('services.whatsapp.version') ?: 'v22.0');
        $phoneId = (string) config('services.whatsapp.phone_number_id');

        return "https://graph.facebook.com/{$version}/{$phoneId}/{$path}";
    }

    /** Kirim pesan teks. Mengembalikan id pesan dari Meta, atau null bila gagal. */
    public function sendText(string $to, string $body): ?string
    {
        try {
            $res = Http::timeout(30)
                ->withToken((string) config('services.whatsapp.token'))
                ->post($this->url('messages'), [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['preview_url' => false, 'body' => mb_substr($body, 0, 4000)],
                ]);
        } catch (\Throwable $e) {
            return null;
        }

        return $res->successful() ? $res->json('messages.0.id') : null;
    }

    /** Tandai pesan pelanggan sudah dibaca (centang biru). Kegagalan diabaikan. */
    public function markRead(string $waMessageId): void
    {
        try {
            Http::timeout(15)
                ->withToken((string) config('services.whatsapp.token'))
                ->post($this->url('messages'), [
                    'messaging_product' => 'whatsapp',
                    'status' => 'read',
                    'message_id' => $waMessageId,
                ]);
        } catch (\Throwable $e) {
            // abaikan
        }
    }
}
