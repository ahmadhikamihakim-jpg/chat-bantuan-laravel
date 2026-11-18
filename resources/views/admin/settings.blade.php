@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('content')
<div class="card">
<h3>Pengaturan Website</h3>
<form method="POST" action="{{ route('admin.settings.update') }}">
@csrf @method('PUT')
<div class="form-grid">
  <div><label for="site_name">Nama website</label><input id="site_name" name="site_name" value="{{ old('site_name', $values['site_name']) }}" maxlength="100"></div>
  <div><label for="tagline">Tagline</label><input id="tagline" name="tagline" value="{{ old('tagline', $values['tagline']) }}" maxlength="150"></div>
  <div><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $values['email']) }}" maxlength="100"></div>
</div>

<h3 style="margin-top:28px">Pengaturan Widget Chat</h3>
<div class="form-grid">
  <div>
    <label for="chat_enabled">Widget chat</label>
    <label style="display:flex;gap:8px;align-items:center;font-weight:400"><input type="checkbox" id="chat_enabled" name="chat_enabled" value="1" @checked(old('chat_enabled', $values['chat_enabled']) === '1') style="width:auto"> Tampilkan widget di website</label>
  </div>
  <div><label for="chat_bot_name">Nama bot</label><input id="chat_bot_name" name="chat_bot_name" value="{{ old('chat_bot_name', $values['chat_bot_name']) }}" maxlength="50"></div>
  <div><label for="chat_bot_status">Status bot (di bawah nama)</label><input id="chat_bot_status" name="chat_bot_status" value="{{ old('chat_bot_status', $values['chat_bot_status']) }}" maxlength="30"></div>
  <div><label for="chat_button_label">Teks tombol melayang</label><input id="chat_button_label" name="chat_button_label" value="{{ old('chat_button_label', $values['chat_button_label']) }}" maxlength="50"></div>
  <div><label for="chat_color">Warna widget (kode hex)</label><input id="chat_color" name="chat_color" value="{{ old('chat_color', $values['chat_color']) }}" maxlength="7" placeholder="#2a27a8"></div>
</div>
<label for="chat_greeting">Teks sapaan bot</label>
<textarea id="chat_greeting" name="chat_greeting" rows="2" maxlength="500">{{ old('chat_greeting', $values['chat_greeting']) }}</textarea>
<label for="chat_fallback">Teks bila bot tidak paham (fallback)</label>
<textarea id="chat_fallback" name="chat_fallback" rows="3" maxlength="1000">{{ old('chat_fallback', $values['chat_fallback']) }}</textarea>
<label for="chat_wa_text">Teks pembuka WhatsApp</label>
<textarea id="chat_wa_text" name="chat_wa_text" rows="2" maxlength="500">{{ old('chat_wa_text', $values['chat_wa_text']) }}</textarea>
<p class="muted small">Tulis {nama} di teks pembuka untuk menyebut nama sales yang dipilih pengunjung, mis. “Halo {nama}, saya dari website…”.</p>

<h3 style="margin-top:28px">Mode AI (jawab otomatis)</h3>
<p class="muted small">Bila aktif, ketikan bebas pengunjung dijawab oleh AI dari Basis Pengetahuan di bawah + jawaban menu yang sudah tersimpan. Tombol menu tetap memakai jawaban instan seperti biasa. Bila AI tidak terjangkau, widget otomatis kembali ke pencocokan kata kunci + fallback.</p>
<div class="form-grid">
  <div>
    <label for="ai_enabled">Mode AI</label>
    <label style="display:flex;gap:8px;align-items:center;font-weight:400"><input type="checkbox" id="ai_enabled" name="ai_enabled" value="1" @checked(old('ai_enabled', $values['ai_enabled']) === '1') style="width:auto"> Aktifkan jawaban AI untuk ketikan bebas</label>
  </div>
  <div>
    <label for="ai_provider">Penyedia AI</label>
    <select id="ai_provider" name="ai_provider">
      <option value="ollama" @selected(old('ai_provider', $values['ai_provider']) === 'ollama')>Ollama — AI lokal di komputer sendiri (gratis, tanpa internet)</option>
      <option value="gemini" @selected(old('ai_provider', $values['ai_provider']) === 'gemini')>Google Gemini API (perlu API key di .env)</option>
    </select>
  </div>
  <div><label for="ai_model_ollama">Nama model Ollama</label><input id="ai_model_ollama" name="ai_model_ollama" value="{{ old('ai_model_ollama', $values['ai_model_ollama']) }}" maxlength="100" placeholder="qwen2.5:3b"></div>
  <div><label for="ai_ollama_url">Alamat server Ollama</label><input id="ai_ollama_url" name="ai_ollama_url" value="{{ old('ai_ollama_url', $values['ai_ollama_url']) }}" maxlength="200" placeholder="http://127.0.0.1:11434"></div>
  <div><label for="ai_model_gemini">Nama model Gemini</label><input id="ai_model_gemini" name="ai_model_gemini" value="{{ old('ai_model_gemini', $values['ai_model_gemini']) }}" maxlength="100" placeholder="gemini-2.5-flash"></div>
</div>
<label for="ai_knowledge">Basis Pengetahuan (bahan jawaban AI)</label>
<textarea id="ai_knowledge" name="ai_knowledge" rows="9" maxlength="8000" placeholder="Tulis di sini semua yang boleh dijawab AI: tentang usaha Anda, daftar produk/jasa, harga, jam operasional, alamat, cara pemesanan, kebijakan, dsb.">{{ old('ai_knowledge', $values['ai_knowledge']) }}</textarea>
<p class="muted small">Untuk Gemini: buat API key di Google AI Studio (aistudio.google.com), lalu tulis <strong>GEMINI_API_KEY=…</strong> di file .env proyek (jangan ditulis di halaman ini). Untuk Ollama: pasang Ollama, jalankan <strong>ollama pull qwen2.5:3b</strong>, dan biarkan Ollama berjalan selama website dipakai.</p>

<h3 style="margin-top:28px">WhatsApp Otomatis (Cloud API resmi Meta)</h3>
<p class="muted small">Bila aktif, chat yang masuk ke nomor WhatsApp bisnis Anda dibalas otomatis memakai otak yang sama dengan widget web: AI dari Basis Pengetahuan di atas (bila Mode AI menyala), kata kunci menu, lalu fallback. Riwayatnya ada di menu <strong>Log WhatsApp</strong>. Balasan hanya dikirim dalam jendela 24 jam sejak pesan terakhir pelanggan, sesuai aturan Meta.</p>
<div class="grid2">
  <div>
    <label for="wa_enabled">Balas WhatsApp Otomatis</label>
    <label style="display:flex;gap:8px;align-items:center;font-weight:400"><input type="checkbox" id="wa_enabled" name="wa_enabled" value="1" @checked(old('wa_enabled', $values['wa_enabled']) === '1') style="width:auto"> Aktifkan balasan otomatis di WhatsApp</label>
  </div>
  <div>
    <label>Status sambungan (dari file .env)</label>
    <p class="small" style="margin:2px 0">
      Token akses: <strong>{{ config('services.whatsapp.token') ? 'terisi' : 'BELUM diisi' }}</strong><br>
      ID nomor pengirim: <strong>{{ config('services.whatsapp.phone_number_id') ? 'terisi' : 'BELUM diisi' }}</strong><br>
      Token verifikasi webhook: <strong>{{ config('services.whatsapp.verify_token') ? 'terisi' : 'BELUM diisi' }}</strong>
    </p>
  </div>
</div>
<p class="muted small">Alamat webhook untuk dasbor Meta: <strong>{{ url('/whatsapp/webhook') }}</strong> — alamat ini harus bisa dibuka dari internet (misalnya lewat Cloudflare Tunnel) dan didaftarkan di aplikasi Meta Anda, dengan token verifikasi yang sama seperti <strong>WHATSAPP_VERIFY_TOKEN</strong> di file .env. Token akses &amp; ID nomor ditulis di .env, tidak di halaman ini. Langkah lengkapnya ada di README proyek bagian "WhatsApp Cloud API".</p>
<div style="margin-top:22px"><button class="btn gold" type="submit">Simpan Pengaturan</button></div>
</form>
</div>
@endsection
