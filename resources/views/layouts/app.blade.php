<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $header ?? 'Dashboard' }} — Chat Bantuan</title>
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body style="background:var(--bg)">
<header class="site-header"><div class="wrap header-in">
  <a class="logo" href="{{ route('home') }}">
    <span class="logo-mark">IV</span>
    <span><strong>Chat <span>Bantuan</span></strong><small>WIDGET CHAT LARAVEL</small></span>
  </a>
  <nav class="main-nav" style="display:flex">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('profile.edit') }}">Profil</a>
    @if(auth()->user()?->is_admin)<a href="{{ route('admin.dashboard') }}">Admin</a>@endif
    <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button class="btn sm" type="submit">Keluar</button></form>
  </nav>
</div></header>
<main class="wrap" style="padding:34px 20px 60px">
  @if(session('status'))<div class="alert success">{{ session('status') }}</div>@endif
  {{ $slot }}
</main>
<footer class="site-footer"><div class="wrap"><div class="footer-bar" style="border:0;margin:0">
  <span>© {{ date('Y') }} Chat Bantuan.</span><span>Widget chat website dengan panel admin.</span>
</div></div></footer>
</body>
</html>
