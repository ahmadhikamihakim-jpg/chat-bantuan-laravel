<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $fillable = ['type', 'content', 'matched'];

    protected $casts = ['matched' => 'boolean'];
}
