<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMenu extends Model
{
    protected $fillable = ['label', 'action', 'answer', 'keywords', 'url', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Daftar kata kunci (huruf kecil) untuk mencocokkan ketikan bebas pengunjung. */
    public function keywordList(): array
    {
        if (! $this->keywords) {
            return [];
        }

        return collect(explode(',', $this->keywords))
            ->map(fn ($k) => trim(mb_strtolower($k)))
            ->filter()
            ->values()
            ->all();
    }
}
