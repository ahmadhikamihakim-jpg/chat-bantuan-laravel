<div class="form-grid">
  <div><label for="name">Nama *</label><input id="name" name="name" value="{{ old('name', $agent->name) }}" maxlength="100" required placeholder="mis. Sales Andi"></div>
  <div><label for="label">Keterangan</label><input id="label" name="label" value="{{ old('label', $agent->label) }}" maxlength="100" placeholder="mis. Sales / Customer Service"></div>
  <div><label for="wa_number">Nomor WhatsApp * (format 62…, tanpa +)</label><input id="wa_number" name="wa_number" value="{{ old('wa_number', $agent->wa_number) }}" maxlength="20" required placeholder="6281234567890"></div>
  <div><label for="sort_order">Urutan tampil</label><input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $agent->sort_order ?? 0) }}" min="0" max="999"></div>
  <div>
    <label>Status</label>
    <label style="display:flex;gap:8px;align-items:center;font-weight:400"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $agent->is_active)) style="width:auto"> Aktif (tampil di widget)</label>
  </div>
</div>
