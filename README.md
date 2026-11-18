# Chat Bantuan — Widget Chat + Panel Admin + AI (Laravel 12)

Widget chat bantuan untuk website, lengkap dengan panel admin. Pengunjung dapat memilih menu pertanyaan yang tersedia, mengetik pertanyaan bebas, atau diteruskan ke tim melalui WhatsApp. Pertanyaan bebas dijawab oleh mode AI bila aktif, dengan cadangan pencocokan kata kunci dari jawaban yang disiapkan admin. Tersedia juga balasan otomatis untuk pesan WhatsApp masuk melalui WhatsApp Cloud API resmi dari Meta.

## Fitur utama

- Widget chat melayang di halaman publik: menu pertanyaan, tanya bebas, dan panel "Bicara dengan Tim" yang membuka WhatsApp agen.
- Mode AI dua penyedia, diatur dari panel admin: Ollama (AI lokal, tanpa kunci) atau Google Gemini. Bahan jawaban berasal dari Basis Pengetahuan, jawaban menu, dan daftar tim yang diisi admin; AI diarahkan menjawab hanya dari bahan tersebut.
- Cadangan pencocokan kata kunci dan pesan fallback bila AI tidak aktif atau gagal menjawab.
- Balasan WhatsApp otomatis lewat WhatsApp Cloud API: verifikasi token dan tanda tangan webhook, penolakan pesan ganda, rantai jawaban AI → kata kunci → fallback, dan pencatatan percakapan di Log WhatsApp.
- Panel admin: Dashboard, Menu Chat & Jawaban, Tim/Sales WhatsApp, Log Chat, Log WhatsApp, Pengguna, Pengaturan, dan Profil.

## Teknologi

- PHP 8.2, Laravel 12, Blade
- MySQL
- JavaScript murni untuk widget chat (tanpa framework)
- Ollama (lokal) / Google Gemini API untuk mode AI
- WhatsApp Cloud API (Meta Graph API) untuk balasan WhatsApp

## Cara menjalankan

1. `composer install`
2. Salin `.env.example` menjadi `.env`, lalu sesuaikan koneksi database MySQL (nama database bawaan: `chat_bantuan`).
3. `php artisan key:generate`
4. `php artisan migrate --seed`
5. `php artisan serve`, lalu buka `http://localhost:8000`

Akun demo hasil seed:

- Admin: `admin@chat.test` / `password`
- Pengguna: `budi@chat.test` / `password`

Catatan: data awal (jawaban menu, nomor WhatsApp tim, Basis Pengetahuan) adalah contoh dan perlu diganti dari panel admin. Mode AI memerlukan Ollama yang berjalan di komputer yang sama, atau `GEMINI_API_KEY` di `.env`. Balasan WhatsApp memerlukan kredensial WhatsApp Cloud API di `.env` dan alamat webhook yang dapat dijangkau publik (misalnya lewat Cloudflare Tunnel untuk pemakaian lokal).

---
Proyek belajar dan portofolio oleh Andy Hakim.
