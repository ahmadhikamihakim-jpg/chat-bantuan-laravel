<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'Akun' }} — Chat Bantuan</title>
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
<main class="auth-shell">
  <section class="auth-card">
    <a class="logo" href="{{ route('home') }}" style="margin-bottom:22px">
      <span class="logo-mark">IV</span>
      <span><strong>Chat <span>Bantuan</span></strong><small>WIDGET CHAT LARAVEL</small></span>
    </a>
    {{ $slot }}
  </section>
</main>
</body>
</html>
