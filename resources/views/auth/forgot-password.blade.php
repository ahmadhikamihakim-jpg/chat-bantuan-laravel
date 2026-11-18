<x-guest-layout>
  <h1>Lupa Kata Sandi</h1>
  <p class="muted">Masukkan email akun Anda. Kami akan mengirim tautan untuk membuat kata sandi baru.</p>
  @if(session('status'))<div class="alert success">{{ session('status') }}</div>@endif
  @if($errors->any())<div class="alert error">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>@error('email')<span class="err">{{ $message }}</span>@enderror</div>
    <button class="btn" type="submit" style="width:100%;justify-content:center">Kirim Tautan Atur Ulang</button>
  </form>
  <div class="auth-links"><a href="{{ route('login') }}">← Kembali masuk</a></div>
</x-guest-layout>
