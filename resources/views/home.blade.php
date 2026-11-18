@extends('layouts.site')
@section('title', 'Beranda')
@section('content')
<section class="hero">
  <div class="hero-inner">
    <h1>Widget Chat untuk Website</h1>
    <p>Halaman ini adalah contoh website untuk mencoba widget chat di pojok kanan bawah — sama seperti contoh yang Anda kirim: pengunjung disapa bot, memilih menu cepat atau mengetik pertanyaan, lalu diteruskan ke WhatsApp tim bila perlu bicara langsung.</p>
    <div class="hero-actions">
      <a href="#" class="btn-light js-open-chat">Coba Widget Chat</a>
      <a href="#cara-kerja" class="btn-ghost">Lihat Cara Kerja</a>
    </div>
  </div>
</section>

<section class="section" id="cara-kerja">
  <h2>Cara Kerja</h2>
  <div class="cards">
    <div class="info-card">
      <b>1. Tombol melayang</b>
      <p>Tombol “Butuh bantuan?” selalu tampil di pojok kanan bawah setiap halaman. Sekali klik, jendela chat terbuka dan bot menyapa lebih dulu.</p>
    </div>
    <div class="info-card">
      <b>2. Menu &amp; jawaban otomatis</b>
      <p>Pengunjung memilih tombol menu (Produk, Rate Card, Cara Pemesanan, dan seterusnya) atau mengetik bebas — bot mencocokkan kata kunci dan menjawab dari teks yang disiapkan.</p>
    </div>
    <div class="info-card">
      <b>3. Teruskan ke WhatsApp tim</b>
      <p>Menu “Bicara dengan Tim” menampilkan daftar sales/CS. Satu klik membuka WhatsApp orang yang dipilih, dengan teks pembuka yang sudah disiapkan.</p>
    </div>
  </div>
</section>

<section class="section alt" id="panel-admin">
  <h2>Semua Diatur dari Panel Admin</h2>
  <ul class="check-list">
    <li>Nama bot, status, teks sapaan, teks jawaban tidak ditemukan (fallback), warna widget, dan tombol nyala/mati.</li>
    <li>Menu cepat: label tombol, isi jawaban, kata kunci untuk ketikan bebas, urutan tampil, dan aktif/nonaktif.</li>
    <li>Daftar tim WhatsApp: nama, keterangan (Sales/CS), dan nomor WhatsApp masing-masing.</li>
    <li>Log Chat: pertanyaan yang diketik pengunjung dan tidak terjawab bot — bahan untuk menambah menu/jawaban baru.</li>
  </ul>
  <p><a class="btn-dark" href="{{ route('login') }}">Masuk Panel Admin</a></p>
</section>
@endsection
