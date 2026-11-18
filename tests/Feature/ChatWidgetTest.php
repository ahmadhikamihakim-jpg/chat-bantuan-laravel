<?php

namespace Tests\Feature;

use App\Models\ChatAgent;
use App\Models\ChatLog;
use App\Models\ChatMenu;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_shows_demo_and_widget(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Widget Chat untuk Website')
            ->assertSee('cwRoot')
            ->assertSee('chat-widget.js');
    }

    public function test_config_returns_bot_menus_and_agents(): void
    {
        $res = $this->getJson('/chat/config')->assertOk()
            ->assertJsonPath('enabled', true)
            ->assertJsonPath('bot_name', 'Lumi')
            ->assertJsonPath('button_label', 'Butuh bantuan?');

        $labels = collect($res->json('menus'))->pluck('label');
        $this->assertTrue($labels->contains('Rate Card'));
        $this->assertTrue($labels->contains('Bicara dengan Tim'));

        $rateCard = collect($res->json('menus'))->firstWhere('label', 'Rate Card');
        $this->assertContains('harga', $rateCard['keywords']);

        $agents = collect($res->json('agents'));
        $this->assertGreaterThanOrEqual(3, $agents->count());
        $this->assertStringStartsWith('https://wa.me/6281200000000?text=', $agents->first()['wa_url']);
        $this->assertStringContainsString(rawurlencode('Sales Contoh 1'), $agents->first()['wa_url']);
    }

    public function test_widget_can_be_disabled_from_settings(): void
    {
        Setting::put('chat_enabled', '0');
        $this->getJson('/chat/config')
            ->assertOk()
            ->assertJsonPath('enabled', false)
            ->assertJsonPath('menus', []);
    }

    public function test_log_endpoint_records_activity(): void
    {
        $this->postJson('/chat/log', ['type' => 'text', 'content' => 'berapa harga tanah', 'matched' => false])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('chat_logs', ['type' => 'text', 'content' => 'berapa harga tanah', 'matched' => false]);
    }

    public function test_admin_can_manage_menus_agents_and_settings(): void
    {
        $admin = User::where('email', 'admin@chat.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Menu chat');
        $this->actingAs($admin)->get('/admin/chat-menus')->assertOk()->assertSee('Rate Card');
        $this->actingAs($admin)->get('/admin/chat-agents')->assertOk()->assertSee('Sales Contoh 1');
        $this->actingAs($admin)->get('/admin/chat-logs')->assertOk();

        $this->actingAs($admin)->post('/admin/chat-menus', [
            'label' => 'Jam Operasional', 'action' => 'answer',
            'answer' => 'Kami buka setiap hari.', 'keywords' => 'jam, buka, operasional',
            'sort_order' => 8, 'is_active' => 1,
        ])->assertRedirect(route('admin.chat-menus.index'));
        $this->assertDatabaseHas('chat_menus', ['label' => 'Jam Operasional']);

        $menu = ChatMenu::where('label', 'Jam Operasional')->firstOrFail();
        $this->actingAs($admin)->put("/admin/chat-menus/{$menu->id}", [
            'label' => 'Jam Operasional', 'action' => 'answer',
            'answer' => 'Kami buka Senin–Sabtu.', 'keywords' => 'jam, buka',
            'sort_order' => 8, 'is_active' => 1,
        ])->assertRedirect(route('admin.chat-menus.index'));
        $this->assertDatabaseHas('chat_menus', ['answer' => 'Kami buka Senin–Sabtu.']);

        $this->actingAs($admin)->post('/admin/chat-agents', [
            'name' => 'Sales Dedi', 'label' => 'Sales', 'wa_number' => '6281299998888',
            'sort_order' => 4, 'is_active' => 1,
        ])->assertRedirect(route('admin.chat-agents.index'));
        $this->assertDatabaseHas('chat_agents', ['name' => 'Sales Dedi', 'wa_number' => '6281299998888']);

        $agent = ChatAgent::where('name', 'Sales Dedi')->firstOrFail();
        $this->assertStringContainsString('Sales%20Dedi', $agent->waUrl());

        $this->actingAs($admin)->put('/admin/settings', [
            'site_name' => 'Website Contoh', 'chat_enabled' => '1', 'chat_bot_name' => 'Sinta',
            'chat_color' => '#123abc',
        ])->assertRedirect();
        $this->getJson('/chat/config')->assertJsonPath('bot_name', 'Sinta')->assertJsonPath('color', '#123abc');
    }

    public function test_admin_pages_reject_guests_and_non_admins(): void
    {
        $this->get('/admin/chat-menus')->assertRedirect('/login');

        $user = User::where('email', 'budi@chat.test')->firstOrFail();
        $this->actingAs($user)->get('/admin/chat-menus')->assertForbidden();
        $this->actingAs($user)->get('/admin/chat-agents')->assertForbidden();
        $this->actingAs($user)->get('/admin/chat-logs')->assertForbidden();
    }

    public function test_menu_validation_requires_answer_for_answer_type(): void
    {
        $admin = User::where('email', 'admin@chat.test')->firstOrFail();
        $this->actingAs($admin)->post('/admin/chat-menus', [
            'label' => 'Menu Kosong', 'action' => 'answer', 'answer' => '',
        ])->assertSessionHasErrors('answer');
    }
}
