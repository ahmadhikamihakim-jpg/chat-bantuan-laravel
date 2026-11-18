<x-guest-layout>
  <h1>Daftar Akun</h1>
  <p class="muted">Buat akun pengguna Chat Bantuan.</p>
  @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="field"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">@error('name')<span class="err">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">@error('email')<span class="err">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="password">Kata Sandi</label><input id="password" type="password" name="password" required autocomplete="new-password">@error('password')<span class="err">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="password_confirmation">Ulangi Kata Sandi</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">@error('password_confirmation')<span class="err">{{ $message }}</span>@enderror</div>
    <button class="btn" type="submit" style="width:100%;justify-content:center">Daftar →</button>
  </form>
  <div class="auth-links"><span>Sudah punya akun?</span><a href="{{ route('login') }}">Masuk</a></div>
</x-guest-layout>
