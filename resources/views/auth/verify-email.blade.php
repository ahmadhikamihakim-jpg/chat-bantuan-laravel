<x-guest-layout>
  <h1>Verifikasi Email</h1>
  <p class="muted">Terima kasih sudah mendaftar. Klik tautan verifikasi yang kami kirim ke email Anda. Jika belum menerima, kami dapat mengirim ulang.</p>
  @if(session('status') === 'verification-link-sent')<div class="alert success">Tautan verifikasi baru sudah dikirim ke email Anda.</div>@endif
  <form method="POST" action="{{ route('verification.send') }}">@csrf<button class="btn" type="submit" style="width:100%;justify-content:center">Kirim Ulang Email Verifikasi</button></form>
  <form method="POST" action="{{ route('logout') }}" style="margin-top:14px;text-align:center">@csrf<button type="submit" style="background:none;border:0;color:var(--green);font:inherit;font-weight:700;cursor:pointer">Keluar</button></form>
</x-guest-layout>
