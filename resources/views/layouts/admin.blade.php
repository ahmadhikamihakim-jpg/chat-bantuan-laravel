<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard') — Admin {{ \App\Models\Setting::get('site_name', 'Chat Bantuan') }}</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <a class="brand-mark" href="{{ route('admin.dashboard') }}">
      <span class="brand-icon"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/></svg></span>
      <span class="brand-copy"><span class="brand-title">{{ \App\Models\Setting::get('site_name', 'Chat Bantuan') }}</span><span class="brand-subtitle">Admin Panel</span></span>
    </a>
  </div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><rect x="3" y="3" width="7.5" height="9" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="5.5" rx="1.5"/><rect x="13.5" y="11.5" width="7.5" height="9.5" rx="1.5"/><rect x="3" y="15" width="7.5" height="6" rx="1.5"/></svg></span><span class="nav-text">Dashboard</span></a>
    <a href="{{ route('admin.chat-menus.index') }}" class="{{ request()->routeIs('admin.chat-menus.*') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-8.5 8.5 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 8.5-8.5 8.38 8.38 0 0 1 8.5 8.5z"/></svg></span><span class="nav-text">Menu Chat &amp; Jawaban</span></a>
    <a href="{{ route('admin.chat-agents.index') }}" class="{{ request()->routeIs('admin.chat-agents.*') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><span class="nav-text">Tim / Sales WhatsApp</span></a>
    <a href="{{ route('admin.chat-logs.index') }}" class="{{ request()->routeIs('admin.chat-logs.*') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg></span><span class="nav-text">Log Chat</span>@php($unmatched = \App\Models\ChatLog::whereIn('type', ['text', 'wa'])->where('matched', false)->count()) @if($unmatched)<span class="sidebar-badge">{{ $unmatched }}</span>@endif</a>
    <a href="{{ route('admin.wa-logs.index') }}" class="{{ request()->routeIs('admin.wa-logs.*') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.13.96.36 1.9.7 2.8a2 2 0 0 1-.45 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.45c.9.34 1.84.57 2.8.7A2 2 0 0 1 22 16.9z"/></svg></span><span class="nav-text">Log WhatsApp</span></a>
    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.8-3.2 3.4-5 6.5-5s5.7 1.8 6.5 5"/><circle cx="17" cy="9" r="2.5"/><path d="M17.8 15.2c1.9.6 3.2 2 3.7 4.3"/></svg></span><span class="nav-text">Pengguna</span></a>
    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 7h9M17 7h3M4 17h3M11 17h9"/><circle cx="15" cy="7" r="2.2"/><circle cx="9" cy="17" r="2.2"/></svg></span><span class="nav-text">Pengaturan</span></a>
    <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'on' : '' }}"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4.5 20.5c1.4-3.8 4.2-5.5 7.5-5.5s6.1 1.7 7.5 5.5"/></svg></span><span class="nav-text">Profil</span></a>
    <span class="menu-sep"></span>
    <a href="{{ route('home') }}" target="_blank"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9S14.5 18.4 12 21c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3z"/></svg></span><span class="nav-text">Lihat Website</span></a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="menu-btn"><span class="nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg></span><span class="nav-text">Keluar</span></button></form>
  </nav>
  <div class="sidebar-user">
    <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
    <div class="user-copy"><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></div>
  </div>
  <div class="sidebar-footer"><span class="status-dot"></span><span class="sidebar-footer-text">Panel admin aktif</span></div>
</aside>
<div class="admin-main">
  <header class="admin-topbar">
    <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Buka/tutup menu samping"><span></span><span></span><span></span></button>
    <form class="topbar-search" method="GET" action="{{ route('admin.users.index') }}">
      <input type="search" name="q" value="{{ request()->routeIs('admin.users.*') ? request('q') : '' }}" placeholder="Cari pengguna…" aria-label="Cari pengguna">
    </form>
    <div class="top-user"><span class="muted top-date">{{ now()->translatedFormat('l, d F Y') }}</span><span class="avatar sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><strong>{{ auth()->user()->name }}</strong></div>
  </header>
  <main class="admin-content">
    @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
    @yield('content')
  </main>
</div>
<script>
(function(){
  var sidebar=document.getElementById('adminSidebar'),backdrop=document.getElementById('sidebarBackdrop'),toggle=document.getElementById('sidebarToggle');
  if(!sidebar||!toggle)return;
  toggle.addEventListener('click',function(){
    if(window.innerWidth<=991){sidebar.classList.toggle('open');backdrop.classList.toggle('show',sidebar.classList.contains('open'));}
    else{document.body.classList.toggle('sidebar-mini');}
  });
  backdrop.addEventListener('click',function(){sidebar.classList.remove('open');backdrop.classList.remove('show');});
})();
</script>
</body>
</html>
