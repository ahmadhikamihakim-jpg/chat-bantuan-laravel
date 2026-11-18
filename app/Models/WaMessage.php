<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaMessage extends Model
{
    protected $fillable = [
        'wa_message_id', 'direction', 'wa_id', 'contact_name',
        'body', 'status', 'is_ai',
    ];

    protected $casts = [
        'is_ai' => 'boolean',
    ];
}
