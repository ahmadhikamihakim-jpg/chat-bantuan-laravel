<div class="form-grid">
  <div><label for="label">Label tombol *</label><input id="label" name="label" value="{{ old('label', $menu->label) }}" maxlength="100" required></div>
  <div>
    <label for="action">Tipe menu *</label>
    <select id="action" name="action">
      <option value="answer" @selected(old('action', $menu->action) === 'answer')>Jawaban teks — bot menjawab dengan teks</option>
      <option value="agents" @selected(old('action', $menu->action) === 'agents')>Daftar tim WhatsApp — tampilkan sales/CS</option>
      <option value="link" @selected(old('action', $menu->action) === 'link')>Buka tautan — buka URL di tab baru</option>
    </select>
  </div>
  <div><label for="sort_order">Urutan tampil</label><input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $menu->sort_order ?? 0) }}" min="0" max="999"></div>
  <div>
    <label>Status</label>
    <label style="display:flex;gap:8px;align-items:center;font-weight:400"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $menu->is_active)) style="width:auto"> Aktif (tampil di widget)</label>
  </div>
</div>
<label for="answer">Jawaban bot (untuk tipe Jawaban teks)</label>
<textarea id="answer" name="answer" rows="5" maxlength="5000">{{ old('answer', $menu->answer) }}</textarea>
<label for="keywords">Kata kunci ketikan bebas (pisahkan koma)</label>
<input id="keywords" name="keywords" value="{{ old('keywords', $menu->keywords) }}" maxlength="500" placeholder="mis. harga, rate, tarif, biaya">
<p class="muted small">Bila pengunjung mengetik pesan yang mengandung salah satu kata kunci ini, bot menjawab dengan jawaban menu ini.</p>
<label for="url">URL (untuk tipe Buka tautan)</label>
<input id="url" name="url" value="{{ old('url', $menu->url) }}" maxlength="255" placeholder="https://…">
