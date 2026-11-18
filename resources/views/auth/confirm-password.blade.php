<x-guest-layout>
  <h1>Konfirmasi Kata Sandi</h1>
  <p class="muted">Ini area aman. Masukkan kata sandi Anda sebelum melanjutkan.</p>
  @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('password.confirm') }}">
    @csrf
    <div class="field"><label for="password">Kata Sandi</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
    <button class="btn" type="submit" style="width:100%;justify-content:center">Konfirmasi</button>
  </form>
</x-guest-layout>
