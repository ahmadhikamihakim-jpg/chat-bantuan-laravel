<?php

namespace Tests\Feature;

use App\Models\ChatMenu;
use App\Models\Setting;
use App\Models\WaMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        config([
            'services.whatsapp.token' => 'token-uji',
            'services.whatsapp.phone_number_id' => '9988776655',
            'services.whatsapp.verify_token' => 'rahasia-webhook',
            'services.whatsapp.app_secret' => '',
            'services.whatsapp.version' => 'v22.0',
        ]);
        Setting::put('wa_enabled', '1');
    }

    private function payload(string $body = 'berapa harganya?', string $id = 'wamid.001', string $type = 'text'): array
    {
        $message = [
            'from' => '6281234567890',
            'id' => $id,
            'timestamp' => '1731500000',
            'type' => $type,
        ];
        if ($type === 'text') {
            $message['text'] = ['body' => $body];
        }

        return [
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => '1234567890',
                'changes' => [[
                    'field' => 'messages',
                    'value' => [
                        'messaging_product' => 'whatsapp',
                        'metadata' => ['phone_number_id' => '9988776655'],
                        'contacts' => [['wa_id' => '6281234567890', 'profile' => ['name' => 'Pelanggan Uji']]],
                        'messages' => [$message],
                    ],
                ]],
            ]],
        ];
    }

    private function fakeAiAndGraph(): void
    {
        Http::fake([
            '127.0.0.1:11434/*' => Http::response([
                'message' => ['role' => 'assistant', 'content' => 'Harga mulai dari Rp 100.000.'],
            ]),
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.keluar']]]),
        ]);
    }

    public function test_verify_returns_challenge_with_correct_token(): void
    {
        $this->get('/whatsapp/webhook?hub_mode=subscribe&hub_verify_token=rahasia-webhook&hub_challenge=CH123')
            ->assertOk()
            ->assertSee('CH123');
    }

    public function test_verify_rejects_wrong_token(): void
    {
        $this->get('/whatsapp/webhook?hub_mode=subscribe&hub_verify_token=salah&hub_challenge=CH123')
            ->assertForbidden();
    }

    public function test_incoming_text_is_answered_by_ai_and_sent_back(): void
    {
        Setting::put('ai_enabled', '1');
        Setting::put('ai_provider', 'ollama');
        $this->fakeAiAndGraph();

        $this->postJson('/whatsapp/webhook', $this->payload())->assertOk();

        $this->assertDatabaseHas('wa_messages', [
            'direction' => 'in', 'wa_id' => '6281234567890',
            'body' => 'berapa harganya?', 'status' => 'answered',
        ]);
        $this->assertDatabaseHas('wa_messages', [
            'direction' => 'out', 'wa_id' => '6281234567890',
            'body' => 'Harga mulai dari Rp 100.000.', 'status' => 'sent', 'is_ai' => true,
        ]);
        $this->assertDatabaseHas('chat_logs', ['type' => 'wa', 'content' => 'berapa harganya?', 'matched' => true]);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'graph.facebook.com/v22.0/9988776655/messages')
            && ($req['to'] ?? null) === '6281234567890'
            && ($req['text']['body'] ?? null) === 'Harga mulai dari Rp 100.000.');
    }

    public function test_duplicate_webhook_is_ignored(): void
    {
        Setting::put('ai_enabled', '1');
        $this->fakeAiAndGraph();

        $this->postJson('/whatsapp/webhook', $this->payload())->assertOk();
        $this->postJson('/whatsapp/webhook', $this->payload())->assertOk();

        $this->assertSame(1, WaMessage::where('direction', 'in')->count());
        $this->assertSame(1, WaMessage::where('direction', 'out')->count());
    }

    public function test_keyword_fallback_when_ai_disabled(): void
    {
        Setting::put('ai_enabled', '0');
        $this->fakeAiAndGraph();
        $menu = ChatMenu::where('label', 'Rate Card')->firstOrFail();

        $this->postJson('/whatsapp/webhook', $this->payload('minta rate card terbaru dong', 'wamid.002'))->assertOk();

        $this->assertDatabaseHas('wa_messages', [
            'direction' => 'out', 'body' => $menu->answer, 'is_ai' => false, 'status' => 'sent',
        ]);
    }

    public function test_no_reply_when_wa_disabled(): void
    {
        Setting::put('wa_enabled', '0');
        $this->fakeAiAndGraph();

        $this->postJson('/whatsapp/webhook', $this->payload())->assertOk();

        $this->assertDatabaseHas('wa_messages', ['direction' => 'in', 'status' => 'ignored']);
        $this->assertSame(0, WaMessage::where('direction', 'out')->count());
        Http::assertNotSent(fn ($req) => str_contains($req->url(), 'graph.facebook.com'));
    }

    public function test_non_text_message_gets_polite_notice(): void
    {
        $this->fakeAiAndGraph();

        $this->postJson('/whatsapp/webhook', $this->payload('', 'wamid.003', 'image'))->assertOk();

        $out = WaMessage::where('direction', 'out')->first();
        $this->assertNotNull($out);
        $this->assertStringContainsString('hanya bisa membaca pesan teks', $out->body);
    }

    public function test_signature_is_enforced_when_app_secret_set(): void
    {
        config(['services.whatsapp.app_secret' => 'secret-uji']);
        Setting::put('ai_enabled', '1');
        $this->fakeAiAndGraph();

        $body = json_encode($this->payload());
        $good = 'sha256='.hash_hmac('sha256', $body, 'secret-uji');

        $this->call('POST', '/whatsapp/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256=salah',
        ], $body)->assertUnauthorized();

        $this->call('POST', '/whatsapp/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => $good,
        ], $body)->assertOk();

        $this->assertSame(1, WaMessage::where('direction', 'out')->count());
    }
}
