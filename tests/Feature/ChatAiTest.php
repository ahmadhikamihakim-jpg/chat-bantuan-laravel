<?php

namespace Tests\Feature;

use App\Models\ChatLog;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatAiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function enableAi(string $provider = 'ollama'): void
    {
        Setting::put('ai_enabled', '1');
        Setting::put('ai_provider', $provider);
    }

    public function test_config_reports_ai_disabled_by_default(): void
    {
        $this->getJson('/chat/config')->assertOk()->assertJsonPath('ai_enabled', false);
    }

    public function test_ask_rejected_when_ai_disabled(): void
    {
        $this->postJson('/chat/ask', ['message' => 'halo'])
            ->assertOk()->assertJsonPath('ok', false);
    }

    public function test_ask_validation_requires_message(): void
    {
        $this->enableAi();
        $this->postJson('/chat/ask', [])->assertStatus(422);
    }

    public function test_ask_via_ollama_returns_answer_and_logs(): void
    {
        $this->enableAi('ollama');
        Http::fake([
            '127.0.0.1:11434/*' => Http::response([
                'message' => ['role' => 'assistant', 'content' => 'Harga mulai dari Rp 100.000.'],
            ]),
        ]);

        $this->postJson('/chat/ask', ['message' => 'berapa harganya?'])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('answer', 'Harga mulai dari Rp 100.000.');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/api/chat')
            && $req['model'] === 'qwen2.5:3b'
            && str_contains(json_encode($req['messages']), 'BASIS PENGETAHUAN'));
        $this->assertDatabaseHas('chat_logs', ['type' => 'text', 'content' => 'berapa harganya?', 'matched' => true]);
    }

    public function test_ask_via_ollama_failure_falls_back(): void
    {
        $this->enableAi('ollama');
        Http::fake(['127.0.0.1:11434/*' => Http::response('down', 500)]);

        $this->postJson('/chat/ask', ['message' => 'halo'])
            ->assertOk()->assertJsonPath('ok', false);
        $this->assertDatabaseHas('chat_logs', ['type' => 'text', 'content' => 'halo', 'matched' => false]);
    }

    public function test_ask_via_gemini_returns_answer(): void
    {
        config(['services.gemini.key' => 'kunci-uji']);
        $this->enableAi('gemini');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => 'Jam buka kami 09.00–17.00 WIB.']]]]],
            ]),
        ]);

        $this->postJson('/chat/ask', [
            'message' => 'buka jam berapa?',
            'history' => [['role' => 'user', 'text' => 'halo'], ['role' => 'model', 'text' => 'Halo!']],
        ])->assertOk()->assertJsonPath('answer', 'Jam buka kami 09.00–17.00 WIB.');
    }

    public function test_ask_via_gemini_without_key_falls_back(): void
    {
        config(['services.gemini.key' => '']);
        $this->enableAi('gemini');
        Http::fake();

        $this->postJson('/chat/ask', ['message' => 'halo'])
            ->assertOk()->assertJsonPath('ok', false);
        $this->assertSame(0, ChatLog::where('matched', true)->count());
    }
}
