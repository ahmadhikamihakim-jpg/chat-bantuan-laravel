<x-guest-layout>
  <h1>Masuk Akun</h1>
  <p class="muted">Masuk untuk membuka dashboard Chat Bantuan.</p>
  @if(session('status'))<div class="alert success">{{ session('status') }}</div>@endif
  @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">@error('email')<span class="err">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="password">Kata Sandi</label><input id="password" type="password" name="password" required autocomplete="current-password">@error('password')<span class="err">{{ $message }}</span>@enderror</div>
    <label style="display:flex;gap:8px;align-items:center;font-weight:600;margin:4px 0 18px"><input type="checkbox" name="remember" style="width:auto"> Ingat saya</label>
    <button class="btn" type="submit" style="width:100%;justify-content:center">Masuk →</button>
  </form>
  <div class="auth-links">
    @if(Route::has('password.request'))<a href="{{ route('password.request') }}">Lupa kata sandi?</a>@endif
    <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
  </div>
</x-guest-layout>
