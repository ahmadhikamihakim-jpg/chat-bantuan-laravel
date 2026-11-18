<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAgent extends Model
{
    protected $fillable = ['name', 'label', 'wa_number', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Tautan WhatsApp dengan teks pembuka yang sudah disiapkan di Pengaturan. */
    public function waUrl(): string
    {
        $text = Setting::get('chat_wa_text', 'Halo, saya dari website dan ingin bertanya.');
        $text = str_replace('{nama}', $this->name, (string) $text);

        return 'https://wa.me/'.$this->wa_number.'?text='.rawurlencode($text);
    }
}
