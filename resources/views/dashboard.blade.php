<x-app-layout>
  <x-slot name="header">Dashboard</x-slot>
  <div class="form-card">
    <h3 style="font-size:24px;margin-bottom:8px">Halo, {{ auth()->user()->name }} 👋</h3>
    <p class="muted">Anda masuk sebagai {{ auth()->user()->email }}. Dari sini Anda dapat memperbarui profil dan kata sandi.</p>
    @if(auth()->user()->is_admin)<div class="alert success">Akun Anda adalah administrator. Panel pengelolaan website tersedia melalui menu Admin.</div>@endif
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:20px">
      <a class="btn" href="{{ route('profile.edit') }}">Perbarui Profil</a>
      @if(auth()->user()->is_admin)<a class="btn dark" href="{{ route('admin.dashboard') }}">Buka Panel Admin →</a>@endif
      <a class="btn dark" href="{{ route('home') }}">Lihat Website</a>
    </div>
  </div>
</x-app-layout>
