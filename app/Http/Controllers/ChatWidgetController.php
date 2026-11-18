<?php

namespace App\Http\Controllers;

use App\Models\ChatAgent;
use App\Models\ChatLog;
use App\Models\ChatMenu;
use App\Models\Setting;
use Illuminate\Http\Request;

class ChatWidgetController extends Controller
{
    /** Konfigurasi widget untuk dirender JavaScript di browser pengunjung. */
    public function config()
    {
        $enabled = Setting::get('chat_enabled', '1') === '1';

        return response()->json([
            'enabled' => $enabled,
            'ai_enabled' => $enabled && \App\Services\ChatAiService::enabled(),
            'bot_name' => Setting::get('chat_bot_name', 'Lumi'),
            'bot_status' => Setting::get('chat_bot_status', 'Online'),
            'button_label' => Setting::get('chat_button_label', 'Butuh bantuan?'),
            'greeting' => Setting::get('chat_greeting', 'Halo! Ada yang bisa aku bantu?'),
            'fallback' => Setting::get('chat_fallback', 'Maaf, aku belum paham. Silakan pilih menu di bawah.'),
            'color' => Setting::get('chat_color', '#2a27a8'),
            'menus' => $enabled ? ChatMenu::where('is_active', true)->orderBy('sort_order')->orderBy('id')
                ->get(['id', 'label', 'action', 'answer', 'keywords', 'url'])
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'label' => $m->label,
                    'action' => $m->action,
                    'answer' => $m->answer,
                    'keywords' => $m->keywordList(),
                    'url' => $m->url,
                ]) : [],
            'agents' => $enabled ? ChatAgent::where('is_active', true)->orderBy('sort_order')->orderBy('id')
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                    'label' => $a->label,
                    'wa_url' => $a->waUrl(),
                ]) : [],
        ]);
    }

    /** Catat aktivitas widget (klik menu, ketikan, klik sales) untuk halaman Log Chat admin. */
    public function log(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:menu,text,agent'],
            'content' => ['nullable', 'string', 'max:500'],
            'matched' => ['nullable', 'boolean'],
        ]);

        ChatLog::create([
            'type' => $data['type'],
            'content' => $data['content'] ?? null,
            'matched' => $data['matched'] ?? false,
        ]);

        return response()->json(['ok' => true]);
    }
}
