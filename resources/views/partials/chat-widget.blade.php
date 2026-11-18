{{-- Widget Chat Bantuan: tombol melayang + jendela chat + panel tim WhatsApp.
     Semua isi (nama bot, menu, jawaban, daftar tim) diatur dari panel admin. --}}
<div class="cw-root" id="cwRoot" hidden>
  <section class="cw-panel" id="cwPanel" aria-label="Jendela chat bantuan">
    <header class="cw-head">
      <span class="cw-avatar" id="cwAvatar">L</span>
      <span class="cw-head-copy"><strong id="cwBotName">Lumi</strong><small id="cwBotStatus">Online</small></span>
      <button type="button" class="cw-icon-btn" id="cwRefresh" aria-label="Mulai ulang percakapan" title="Mulai ulang">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
      </button>
      <button type="button" class="cw-icon-btn" id="cwClose" aria-label="Tutup chat" title="Tutup">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </header>
    <div class="cw-body" id="cwBody"></div>
    <form class="cw-inputbar" id="cwForm">
      <input type="text" id="cwInput" placeholder="Tulis pesan…" autocomplete="off" maxlength="500" aria-label="Tulis pesan">
      <button type="submit" class="cw-send" aria-label="Kirim pesan">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.4 20.4 21.8 12 3.4 3.6l-.01 6.53L14 12 3.39 13.87z"/></svg>
      </button>
    </form>
  </section>
  <button type="button" class="cw-fab" id="cwFab" aria-label="Buka chat bantuan">
    <span class="cw-fab-avatar" id="cwFabAvatar">L</span>
    <span class="cw-fab-label" id="cwFabLabel">Butuh bantuan?</span>
  </button>
</div>
<link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
<script src="{{ asset('js/chat-widget.js') }}" defer></script>
