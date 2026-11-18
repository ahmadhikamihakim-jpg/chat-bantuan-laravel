<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', $s['site_name'] ?? 'Website Contoh') — {{ $s['site_name'] ?? 'Website Contoh' }}</title>
<link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
<header class="site-header">
  <a class="site-brand" href="{{ route('home') }}">
    <span class="site-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M12 2.7 21 7.5v9L12 21.3 3 16.5v-9z"/><path d="M3 7.5l9 4.8 9-4.8M12 12.3v9"/></svg></span>
    <span><strong>{{ $s['site_name'] ?? 'Website Contoh' }}</strong><small>{{ $s['tagline'] ?? '' }}</small></span>
  </a>
  <nav class="site-nav">
    <a href="{{ route('home') }}">Beranda</a>
    <a href="#cara-kerja">Cara Kerja</a>
    <a href="#panel-admin">Panel Admin</a>
  </nav>
  <a href="#" class="site-cta js-open-chat">Hubungi Kami
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
  </a>
</header>

<main>@yield('content')</main>

<footer class="site-footer">
  <span>© {{ date('Y') }} {{ $s['site_name'] ?? 'Website Contoh' }}.</span>
  <span>{{ $s['email'] ?? '' }}</span>
</footer>

@include('partials.chat-widget')
</body>
</html>
