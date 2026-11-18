@extends('layouts.admin')
@section('title', 'Menu Chat & Jawaban')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
  <div><h1 style="margin:0">Menu Chat &amp; Jawaban Bot</h1>
  <p class="muted" style="margin:4px 0 0">Tombol-tombol yang tampil di jendela chat, beserta jawaban dan kata kuncinya.</p></div>
  <a class="btn" href="{{ route('admin.chat-menus.create') }}">+ Tambah Menu</a>
</div>
<div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Urutan</th><th>Label Tombol</th><th>Tipe</th><th>Kata Kunci</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse($menus as $m)
    <tr>
      <td>{{ $m->sort_order }}</td>
      <td><strong>{{ $m->label }}</strong></td>
      <td>{{ ['answer' => 'Jawaban teks', 'agents' => 'Daftar tim WhatsApp', 'link' => 'Buka tautan'][$m->action] }}</td>
      <td class="small muted">{{ \Illuminate\Support\Str::limit($m->keywords, 50) ?: '—' }}</td>
      <td><span class="chip {{ $m->is_active ? '' : 'gray' }}">{{ $m->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
      <td style="white-space:nowrap">
        <a class="btn sm gold" href="{{ route('admin.chat-menus.edit', $m) }}">Ubah</a>
        <form method="POST" action="{{ route('admin.chat-menus.destroy', $m) }}" style="display:inline" onsubmit="return confirm('Hapus menu ini?')">@csrf @method('DELETE')<button class="btn sm" type="submit">Hapus</button></form>
      </td>
    </tr>
  @empty <tr><td colspan="6" class="muted">Belum ada menu chat.</td></tr> @endforelse
  </tbody>
</table></div>
<div style="margin-top:14px">{{ $menus->links() }}</div>
@endsection
