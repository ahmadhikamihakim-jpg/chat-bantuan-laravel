<x-guest-layout>
  <h1>Buat Kata Sandi Baru</h1>
  <p class="muted">Silakan buat kata sandi baru untuk akun Anda.</p>
  @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ request()->route('token') }}">
    <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', request('email')) }}" required autofocus></div>
    <div class="field"><label for="password">Kata Sandi Baru</label><input id="password" type="password" name="password" required autocomplete="new-password"></div>
    <div class="field"><label for="password_confirmation">Ulangi Kata Sandi Baru</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div>
    <button class="btn" type="submit" style="width:100%;justify-content:center">Simpan Kata Sandi</button>
  </form>
</x-guest-layout>
