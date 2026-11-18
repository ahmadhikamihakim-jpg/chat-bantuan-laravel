@extends('layouts.admin')
@section('title', 'Log Chat')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
  <div><h1 style="margin:0">Log Chat</h1>
  <p class="muted" style="margin:4px 0 0">Klik menu, ketikan pengunjung, dan klik sales — ketikan berstatus Fallback artinya bot belum punya jawabannya.</p></div>
  <form method="POST" action="{{ route('admin.chat-logs.clear') }}" onsubmit="return confirm('Kosongkan semua log chat?')">@csrf @method('DELETE')<button class="btn" type="submit">Kosongkan Log</button></form>
</div>
<form method="GET" class="flex" style="margin-bottom:12px;gap:8px;display:flex;flex-wrap:wrap">
  <select name="type" style="max-width:220px">
    <option value="">Semua jenis</option>
    <option value="menu" @selected(request('type') === 'menu')>Klik menu</option>
    <option value="text" @selected(request('type') === 'text')>Ketik bebas</option>
    <option value="agent" @selected(request('type') === 'agent')>Klik sales WA</option>
    <option value="wa" @selected(request('type') === 'wa')>WhatsApp masuk</option>
  </select>
  <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="unmatched" value="1" @checked(request('unmatched')) style="width:auto"> Hanya yang tidak terjawab</label>
  <button class="btn small" type="submit">Saring</button>
</form>
<div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Waktu</th><th>Jenis</th><th>Isi</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse($logs as $l)
    <tr>
      <td class="small muted" style="white-space:nowrap">{{ $l->created_at->translatedFormat('d M Y H:i') }}</td>
      <td>{{ ['menu' => 'Klik menu', 'text' => 'Ketik bebas', 'agent' => 'Klik sales WA', 'wa' => 'WhatsApp masuk'][$l->type] ?? $l->type }}</td>
      <td>{{ $l->content }}</td>
      <td>@if(in_array($l->type, ['text', 'wa']))<span class="chip {{ $l->matched ? 'gray' : '' }}">{{ $l->matched ? 'Terjawab' : 'Fallback' }}</span>@else — @endif</td>
      <td><form method="POST" action="{{ route('admin.chat-logs.destroy', $l) }}" onsubmit="return confirm('Hapus log ini?')">@csrf @method('DELETE')<button class="btn sm" type="submit">Hapus</button></form></td>
    </tr>
  @empty <tr><td colspan="5" class="muted">Belum ada log.</td></tr> @endforelse
  </tbody>
</table></div>
<div style="margin-top:14px">{{ $logs->links() }}</div>
@endsection
