<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class HomeController extends Controller
{
    /** Pengaturan situs yang dipakai halaman publik. */
    public static function settings(): array
    {
        return [
            'site_name' => Setting::get('site_name', 'Website Contoh'),
            'tagline' => Setting::get('tagline', 'Halaman contoh untuk mencoba widget chat'),
            'email' => Setting::get('email', 'halo@contoh.test'),
        ];
    }

    public function index()
    {
        return view('home', ['s' => self::settings()]);
    }
}
