@extends('layouts.admin')
@section('title', 'Tim / Sales WhatsApp')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:14px">
  <div><h1 style="margin:0">Tim / Sales WhatsApp</h1>
  <p class="muted" style="margin:4px 0 0">Daftar orang yang tampil saat pengunjung memilih “Bicara dengan Tim”.</p></div>
  <a class="btn" href="{{ route('admin.chat-agents.create') }}">+ Tambah Tim</a>
</div>
<div class="tbl-wrap"><table class="tbl">
  <thead><tr><th>Urutan</th><th>Nama</th><th>Keterangan</th><th>Nomor WhatsApp</th><th>Status</th><th></th></tr></thead>
  <tbody>
  @forelse($agents as $a)
    <tr>
      <td>{{ $a->sort_order }}</td>
      <td><strong>{{ $a->name }}</strong></td>
      <td>{{ $a->label ?: '—' }}</td>
      <td>{{ $a->wa_number }}</td>
      <td><span class="chip {{ $a->is_active ? '' : 'gray' }}">{{ $a->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
      <td style="white-space:nowrap">
        <a class="btn sm gold" href="{{ route('admin.chat-agents.edit', $a) }}">Ubah</a>
        <form method="POST" action="{{ route('admin.chat-agents.destroy', $a) }}" style="display:inline" onsubmit="return confirm('Hapus anggota tim ini?')">@csrf @method('DELETE')<button class="btn sm" type="submit">Hapus</button></form>
      </td>
    </tr>
  @empty <tr><td colspan="6" class="muted">Belum ada tim WhatsApp.</td></tr> @endforelse
  </tbody>
</table></div>
<div style="margin-top:14px">{{ $agents->links() }}</div>
@endsection
