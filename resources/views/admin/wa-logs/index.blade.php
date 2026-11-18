@extends('layouts.admin')
@section('title', 'Log WhatsApp')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
  <div><h1 style="margin:0">Log WhatsApp</h1>
  <p class="muted" style="margin:4px 0 0">Percakapan WhatsApp lewat Cloud API: pesan pelanggan dan balasan otomatis bot (AI / kata kunci / fallback).</p></div>
  <form method="POST" action="{{ route('admin.wa-logs.clear') }}" onsubmit="return confirm('Kosongkan semua log WhatsApp? Riwayat percakapan bot ikut terhapus.')">@csrf @method('DELETE')<button class="btn" type="submit">Kosongkan Log</button></form>
</div>
<form method="GET" class="flex" style="margin-bottom:12px;gap:8px;display:flex;flex-wrap:wrap">
  <select name="direction" style="max-width:220px">
    <option value="">Semua arah</option>
    <option value="in" @selected(request('direction') === 'in')>Pesan masuk (pelanggan)</option>
    <option value="out" @selected(request('direction') === 'out')>Balasan bot</option>
  </select>
  <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor / isi pesan" style="max-width:260px">
  <button class="btn small" type="submit">Saring</button>
</form>
<div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Waktu</th><th>Arah</th><th>Nomor</th><th>Isi</th><th>Status</th></tr></thead>
  <tbody>
  @forelse($logs as $l)
    <tr>
      <td class="small muted" style="white-space:nowrap">{{ $l->created_at->translatedFormat('d M Y H:i') }}</td>
      <td>{{ $l->direction === 'in' ? 'Masuk' : 'Balasan bot' }}@if($l->is_ai) <span class="chip gray">AI</span>@endif</td>
      <td style="white-space:nowrap">{{ $l->wa_id }}@if($l->contact_name)<br><span class="small muted">{{ $l->contact_name }}</span>@endif</td>
      <td style="white-space:pre-wrap">{{ $l->body }}</td>
      <td>{{ ['received' => 'Diterima', 'answered' => 'Terjawab', 'sent' => 'Terkirim', 'failed' => 'Gagal', 'ignored' => 'Diabaikan (WA nonaktif)'][$l->status] ?? $l->status }}</td>
    </tr>
  @empty <tr><td colspan="5" class="muted">Belum ada percakapan WhatsApp.</td></tr> @endforelse
  </tbody>
</table></div>
<div style="margin-top:14px">{{ $logs->links() }}</div>
@endsection
