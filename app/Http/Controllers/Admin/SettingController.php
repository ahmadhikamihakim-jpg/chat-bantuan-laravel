<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const KEYS = ['site_name', 'tagline', 'email',
        'chat_enabled', 'chat_bot_name', 'chat_bot_status', 'chat_button_label',
        'chat_greeting', 'chat_fallback', 'chat_wa_text', 'chat_color',
        'ai_enabled', 'ai_provider', 'ai_model_ollama', 'ai_model_gemini', 'ai_ollama_url', 'ai_knowledge',
        'wa_enabled'];

    public function index()
    {
        $values = [];
        foreach (self::KEYS as $k) {
            $values[$k] = Setting::get($k, '');
        }

        return view('admin.settings', ['values' => $values]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['nullable', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:100'],
            'chat_enabled' => ['nullable', 'boolean'],
            'chat_bot_name' => ['nullable', 'string', 'max:50'],
            'chat_bot_status' => ['nullable', 'string', 'max:30'],
            'chat_button_label' => ['nullable', 'string', 'max:50'],
            'chat_greeting' => ['nullable', 'string', 'max:500'],
            'chat_fallback' => ['nullable', 'string', 'max:1000'],
            'chat_wa_text' => ['nullable', 'string', 'max:500'],
            'chat_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'ai_enabled' => ['nullable', 'boolean'],
            'ai_provider' => ['nullable', 'string', 'in:ollama,gemini'],
            'ai_model_ollama' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9._:\/-]+$/'],
            'ai_model_gemini' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9._:\/-]+$/'],
            'ai_ollama_url' => ['nullable', 'url', 'max:200'],
            'ai_knowledge' => ['nullable', 'string', 'max:8000'],
            'wa_enabled' => ['nullable', 'boolean'],
        ]);
        $data['chat_enabled'] = $request->boolean('chat_enabled') ? '1' : '0';
        $data['ai_enabled'] = $request->boolean('ai_enabled') ? '1' : '0';
        $data['wa_enabled'] = $request->boolean('wa_enabled') ? '1' : '0';
        foreach (self::KEYS as $k) {
            Setting::put($k, $data[$k] ?? null);
        }

        return back()->with('success', 'Pengaturan tersimpan.');
    }
}
