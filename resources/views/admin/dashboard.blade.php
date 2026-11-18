@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="stat-grid">
  <div class="stat-card"><b>{{ $totalMenu }}</b><span>Menu chat &amp; jawaban bot</span></div>
  <div class="stat-card gold"><b>{{ $totalAgen }}</b><span>Tim / sales WhatsApp</span></div>
  <div class="stat-card"><b>{{ $logHariIni }}</b><span>Aktivitas chat hari ini</span></div>
  <div class="stat-card gold"><b>{{ $tidakTerjawab }}</b><span>Ketikan belum terjawab bot</span></div>
</div>
<div class="card">
  <h3>Aktivitas Chat Terbaru</h3>
  <div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>Jenis</th><th>Isi</th><th>Status</th><th>Waktu</th></tr></thead>
    <tbody>
    @forelse($latestLogs as $l)
      <tr>
        <td>{{ ['menu' => 'Klik menu', 'text' => 'Ketik bebas', 'agent' => 'Klik sales WA'][$l->type] ?? $l->type }}</td>
        <td>{{ \Illuminate\Support\Str::limit($l->content, 80) }}</td>
        <td>@if($l->type === 'text')<span class="chip {{ $l->matched ? 'gray' : '' }}">{{ $l->matched ? 'Terjawab' : 'Fallback' }}</span>@else — @endif</td>
        <td class="small muted">{{ $l->created_at->translatedFormat('d M Y H:i') }}</td>
      </tr>
    @empty <tr><td colspan="4" class="muted">Belum ada aktivitas chat. Buka halaman utama dan coba widget-nya.</td></tr> @endforelse
    </tbody>
  </table></div>
</div>
@if($topFallback->isNotEmpty())
<div class="card">
  <h3>Pertanyaan Paling Sering Tidak Terjawab</h3>
  <p class="muted small">Jadikan kata kunci baru di menu Menu Chat &amp; Jawaban agar bot bisa menjawabnya lain kali.</p>
  <div class="tbl-wrap"><table class="tbl">
    <thead><tr><th>Ketikan pengunjung</th><th>Jumlah</th></tr></thead>
    <tbody>
    @foreach($topFallback as $f)
      <tr><td>{{ $f->content }}</td><td>{{ $f->total }}×</td></tr>
    @endforeach
    </tbody>
  </table></div>
</div>
@endif
@endsection
