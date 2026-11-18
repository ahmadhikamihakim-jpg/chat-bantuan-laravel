<?php

namespace Database\Seeders;

use App\Models\ChatAgent;
use App\Models\ChatMenu;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Pengguna demo ----------
        User::updateOrCreate(['email' => 'admin@chat.test'], [
            'name' => 'Admin Chat', 'password' => 'password', 'is_admin' => true,
        ]);
        User::updateOrCreate(['email' => 'budi@chat.test'], [
            'name' => 'Budi Pengguna', 'password' => 'password', 'is_admin' => false,
        ]);

        // ---------- Pengaturan website + widget chat ----------
        $settings = [
            'site_name' => 'Website Contoh',
            'tagline' => 'Halaman contoh untuk mencoba widget chat',
            'email' => 'halo@contoh.test',
            // Widget chat
            'chat_enabled' => '1',
            'chat_bot_name' => 'Lumi',
            'chat_bot_status' => 'Online',
            'chat_button_label' => 'Butuh bantuan?',
            'chat_greeting' => 'Halo! Aku Lumi. Ada yang bisa aku bantu?',
            'chat_fallback' => 'Maaf, aku belum paham pertanyaan itu. Silakan pilih menu di bawah, atau klik "Bicara dengan Tim" untuk terhubung ke tim kami lewat WhatsApp.',
            'chat_wa_text' => 'Halo {nama}, saya dari website dan ingin bertanya.',
            'chat_color' => '#2a27a8',
            // Mode AI (jawab otomatis) — bawaan mati; aktifkan di Pengaturan.
            'ai_enabled' => '0',
            'ai_provider' => 'ollama',
            'ai_model_ollama' => 'qwen2.5:3b',
            'ai_ollama_url' => 'http://127.0.0.1:11434',
            'ai_model_gemini' => 'gemini-2.5-flash',
            'ai_knowledge' => "CONTOH — GANTI DENGAN DATA ASLI DI PANEL ADMIN (Pengaturan > Basis Pengetahuan).\n\nWebsite Contoh adalah halaman demo untuk widget chat. Layanan contoh kami buka Senin–Jumat pukul 09.00–17.00 WIB. Untuk pertanyaan di luar contoh ini, pengunjung diarahkan ke tim lewat WhatsApp.",
            // WhatsApp Cloud API — bawaan mati; aktifkan di Pengaturan setelah .env terisi.
            'wa_enabled' => '0',
        ];
        foreach ($settings as $k => $v) {
            Setting::put($k, $v);
        }

        // ---------- Menu cepat + jawaban bot (contoh, ubah di panel admin) ----------
        $menus = [
            ['label' => 'Produk', 'action' => 'answer', 'sort_order' => 1, 'keywords' => 'produk, layanan, katalog',
                'answer' => 'Kami menyediakan beberapa produk/layanan utama. Tuliskan nama produk yang ingin ditanyakan, atau klik "Bicara dengan Tim" untuk dibantu memilih yang sesuai kebutuhan Anda. (Teks contoh — ubah di panel admin menu Menu Chat.)'],
            ['label' => 'Rate Card', 'action' => 'answer', 'sort_order' => 2, 'keywords' => 'harga, rate, tarif, biaya, price',
                'answer' => 'Untuk daftar harga / rate card terbaru, tim kami akan mengirimkannya lewat WhatsApp agar sesuai kebutuhan Anda. Klik menu "Bicara dengan Tim" lalu pilih salah satu sales. (Teks contoh — ubah di panel admin.)'],
            ['label' => 'Rekomendasi Produk', 'action' => 'answer', 'sort_order' => 3, 'keywords' => 'rekomendasi, saran, cocok, bingung pilih',
                'answer' => 'Ceritakan kebutuhan Anda secara singkat (tujuannya apa dan untuk siapa), nanti saya arahkan ke pilihan yang sesuai. Atau langsung konsultasi lewat menu "Bicara dengan Tim". (Teks contoh — ubah di panel admin.)'],
            ['label' => 'Cara Pemesanan', 'action' => 'answer', 'sort_order' => 4, 'keywords' => 'pesan, order, beli, pemesanan, cara',
                'answer' => "Cara pemesanan:\n1. Pilih produk yang diinginkan.\n2. Hubungi tim kami lewat WhatsApp (menu \"Bicara dengan Tim\").\n3. Tim kami konfirmasi detail dan ketersediaannya.\n4. Selesaikan pembayaran sesuai instruksi tim, pesanan diproses. (Teks contoh — ubah di panel admin.)"],
            ['label' => 'Digital Marketing', 'action' => 'answer', 'sort_order' => 5, 'keywords' => 'digital, marketing, iklan, promosi, sosmed, sosial media',
                'answer' => 'Selain layanan utama, kami juga membantu kebutuhan promosi digital. Ceritakan kebutuhan promosi Anda, atau klik "Bicara dengan Tim" untuk konsultasi langsung. (Teks contoh — ubah di panel admin.)'],
            ['label' => 'Keluhan & Masukan', 'action' => 'answer', 'sort_order' => 6, 'keywords' => 'keluhan, komplain, masukan, kecewa, rusak',
                'answer' => 'Mohon maaf atas ketidaknyamanannya. Sampaikan keluhan atau masukan Anda lewat WhatsApp tim kami (menu "Bicara dengan Tim") agar bisa segera ditindaklanjuti. (Teks contoh — ubah di panel admin.)'],
            ['label' => 'Bicara dengan Tim', 'action' => 'agents', 'sort_order' => 7, 'keywords' => 'tim, sales, manusia, admin, cs, bicara, hubungi, kontak',
                'answer' => null],
        ];
        foreach ($menus as $m) {
            ChatMenu::updateOrCreate(['label' => $m['label']], $m + ['is_active' => true]);
        }

        // ---------- Tim / sales WhatsApp (CONTOH — ganti nomornya di panel admin) ----------
        $agents = [
            ['name' => 'Sales Contoh 1', 'label' => 'Sales', 'wa_number' => '6281200000000', 'sort_order' => 1],
            ['name' => 'Sales Contoh 2', 'label' => 'Sales', 'wa_number' => '6281200000000', 'sort_order' => 2],
            ['name' => 'CS Contoh', 'label' => 'Customer Service', 'wa_number' => '6281200000000', 'sort_order' => 3],
        ];
        foreach ($agents as $a) {
            ChatAgent::updateOrCreate(['name' => $a['name']], $a + ['is_active' => true]);
        }
    }
}
