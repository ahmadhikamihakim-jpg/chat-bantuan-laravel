/* Widget Chat Bantuan — bot berbasis aturan (menu + kata kunci), diteruskan ke WhatsApp tim.
   Konfigurasi diambil dari GET /chat/config (diatur lewat panel admin). */
(function () {
  var root = document.getElementById('cwRoot');
  if (!root) return;

  var panel = document.getElementById('cwPanel');
  var body = document.getElementById('cwBody');
  var form = document.getElementById('cwForm');
  var input = document.getElementById('cwInput');
  var csrf = document.querySelector('meta[name="csrf-token"]');
  var cfg = null;
  var started = false;
  var history = [];
  var typingEl = null;

  var WA_PATH = 'M17.5 14.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.48-1.77-1.65-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.5 0 1.48 1.07 2.9 1.22 3.1.15.2 2.1 3.2 5.1 4.49.71.31 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35M12.04 21.79h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.9-9.88a9.83 9.83 0 0 1 9.88 9.89c0 5.45-4.44 9.88-9.89 9.88m8.42-18.3A11.82 11.82 0 0 0 12.04 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.9 11.9 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.9 0-3.18-1.24-6.16-3.48-8.41';

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function log(type, content, matched) {
    if (!csrf) return;
    fetch('/chat/log', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf.content },
      body: JSON.stringify({ type: type, content: content, matched: !!matched }),
      keepalive: true
    }).catch(function () {});
  }

  function scrollDown() { body.scrollTop = body.scrollHeight; }

  function addBot(text) {
    var row = document.createElement('div');
    row.className = 'cw-row';
    row.innerHTML = '<span class="cw-mini-avatar">' + esc(initial()) + '</span><div class="cw-bubble bot">' + esc(text) + '</div>';
    body.appendChild(row);
    scrollDown();
  }

  function addMe(text) {
    var row = document.createElement('div');
    row.className = 'cw-row me';
    row.innerHTML = '<div class="cw-bubble me">' + esc(text) + '</div>';
    body.appendChild(row);
    scrollDown();
  }

  function initial() { return (cfg && cfg.bot_name ? cfg.bot_name.charAt(0) : 'L').toUpperCase(); }

  function pushHistory(role, text) {
    history.push({ role: role, text: text });
    if (history.length > 12) history = history.slice(-12);
  }

  function addTyping() {
    removeTyping();
    var row = document.createElement('div');
    row.className = 'cw-row';
    row.innerHTML = '<span class="cw-mini-avatar">' + esc(initial()) + '</span><div class="cw-bubble bot cw-typing"><span></span><span></span><span></span></div>';
    body.appendChild(row);
    typingEl = row;
    scrollDown();
  }

  function removeTyping() { if (typingEl) { typingEl.remove(); typingEl = null; } }

  function askAi(text) {
    addTyping();
    fetch('/chat/ask', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf ? csrf.content : '' },
      body: JSON.stringify({ message: text, history: history.slice(0, -1) })
    })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        removeTyping();
        if (d && d.ok && d.answer) { addBot(d.answer); pushHistory('model', d.answer); }
        else { answerFreeText(text); }
      })
      .catch(function () { removeTyping(); answerFreeText(text); });
  }

  function renderChips() {
    var wrap = document.createElement('div');
    wrap.className = 'cw-chips';
    cfg.menus.forEach(function (m) {
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'cw-chip';
      b.textContent = m.label;
      b.addEventListener('click', function () { chooseMenu(m, true); });
      wrap.appendChild(b);
    });
    body.appendChild(wrap);
    scrollDown();
  }

  function renderAgents() {
    addBot('Siap, silakan pilih salah satu tim kami — percakapan akan lanjut di WhatsApp:');
    var box = document.createElement('div');
    box.className = 'cw-agents';
    var html = '';
    cfg.agents.forEach(function (a) {
      html += '<button type="button" class="cw-agent" data-wa="' + esc(a.wa_url) + '" data-name="' + esc(a.name) + '">'
        + '<span class="cw-wa-ic"><svg viewBox="0 0 24 24"><path d="' + WA_PATH + '"/></svg></span>'
        + '<span class="cw-agent-copy"><strong>' + esc(a.name) + '</strong><small>' + esc(a.label || '') + '</small></span>'
        + '<svg class="cw-wa-small" viewBox="0 0 24 24"><path d="' + WA_PATH + '"/></svg></button>';
    });
    box.innerHTML = html || '<div class="cw-agents-title">Belum ada tim yang aktif. Atur di panel admin.</div>';
    box.querySelectorAll('.cw-agent').forEach(function (btn) {
      btn.addEventListener('click', function () {
        log('agent', btn.getAttribute('data-name'), true);
        window.open(btn.getAttribute('data-wa'), '_blank');
      });
    });
    body.appendChild(box);
    scrollDown();
  }

  function chooseMenu(m, showAsUser) {
    if (showAsUser) addMe(m.label);
    log('menu', m.label, true);
    if (m.action === 'agents') { renderAgents(); return; }
    if (m.action === 'link' && m.url) {
      addBot('Baik, saya buka halamannya untuk Anda.');
      window.open(m.url, '_blank');
      return;
    }
    addBot(m.answer || cfg.fallback);
    pushHistory('model', m.answer || cfg.fallback);
  }

  function answerFreeText(text) {
    var t = ' ' + text.toLowerCase() + ' ';
    var found = null;
    for (var i = 0; i < cfg.menus.length; i++) {
      var m = cfg.menus[i];
      for (var j = 0; j < (m.keywords || []).length; j++) {
        var kw = String(m.keywords[j]).trim();
        if (kw && t.indexOf(kw) !== -1) { found = m; break; }
      }
      if (found) break;
    }
    log('text', text, !!found);
    if (!found) { addBot(cfg.fallback); return; }
    if (found.action === 'agents') { renderAgents(); return; }
    if (found.action === 'link' && found.url) { window.open(found.url, '_blank'); addBot('Saya buka halamannya untuk Anda.'); pushHistory('model', 'Saya buka halamannya untuk Anda.'); return; }
    addBot(found.answer || cfg.fallback);
    pushHistory('model', found.answer || cfg.fallback);
  }

  function start() {
    body.innerHTML = '';
    history = [];
    removeTyping();
    addBot(cfg.greeting);
    renderChips();
    started = true;
  }

  function toggle(open) {
    var willOpen = typeof open === 'boolean' ? open : !panel.classList.contains('open');
    panel.classList.toggle('open', willOpen);
    if (willOpen && !started) start();
    if (willOpen) setTimeout(function () { input.focus(); }, 60);
  }

  window.openChatWidget = function () { toggle(true); };

  fetch('/chat/config', { headers: { 'Accept': 'application/json' } })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      cfg = data;
      if (!cfg.enabled) { root.remove(); return; }
      root.hidden = false;
      root.style.setProperty('--cw', cfg.color || '#2a27a8');
      document.getElementById('cwBotName').textContent = cfg.bot_name;
      document.getElementById('cwBotStatus').textContent = cfg.bot_status;
      document.getElementById('cwAvatar').textContent = initial();
      document.getElementById('cwFabAvatar').textContent = initial();
      document.getElementById('cwFabLabel').textContent = cfg.button_label;

      document.getElementById('cwFab').addEventListener('click', function () { toggle(); });
      document.getElementById('cwClose').addEventListener('click', function () { toggle(false); });
      document.getElementById('cwRefresh').addEventListener('click', start);
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var text = input.value.trim();
        if (!text) return;
        input.value = '';
        addMe(text);
        pushHistory('user', text);
        if (cfg.ai_enabled && csrf) { askAi(text); }
        else { setTimeout(function () { answerFreeText(text); }, 250); }
      });
      document.querySelectorAll('.js-open-chat').forEach(function (el) {
        el.addEventListener('click', function (e) { e.preventDefault(); toggle(true); });
      });
    })
    .catch(function () { /* widget gagal dimuat: halaman tetap normal */ });
})();
