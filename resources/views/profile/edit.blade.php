<x-app-layout>
  <x-slot name="header">Profil Saya</x-slot>
  <div style="display:grid;gap:22px;max-width:760px">
    <section class="form-card">
      <h3 style="font-size:23px">Informasi Profil</h3>
      <p class="muted">Perbarui nama dan alamat email akun Anda.</p>
      @if(session('status') === 'profile-updated')<div class="alert success">Profil berhasil diperbarui.</div>@endif
      <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PATCH')
        <div class="field"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name')<span class="err">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>@error('email')<span class="err">{{ $message }}</span>@enderror</div>
        <button class="btn green" type="submit">Simpan Profil</button>
      </form>
    </section>
    <section class="form-card">
      <h3 style="font-size:23px">Ubah Kata Sandi</h3>
      <p class="muted">Gunakan kata sandi yang panjang dan acak agar akun tetap aman.</p>
      @if(session('status') === 'password-updated')<div class="alert success">Kata sandi berhasil diperbarui.</div>@endif
      <form method="POST" action="{{ route('password.update') }}">
        @csrf @method('PUT')
        <div class="field"><label for="current_password">Kata Sandi Saat Ini</label><input id="current_password" type="password" name="current_password" autocomplete="current-password">@error('current_password', 'updatePassword')<span class="err">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password">Kata Sandi Baru</label><input id="password" type="password" name="password" autocomplete="new-password">@error('password', 'updatePassword')<span class="err">{{ $message }}</span>@enderror</div>
        <div class="field"><label for="password_confirmation">Ulangi Kata Sandi Baru</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"></div>
        <button class="btn green" type="submit">Simpan Kata Sandi</button>
      </form>
    </section>
    <section class="form-card" style="border-color:#efb7b1">
      <h3 style="font-size:23px;color:#a12b2b">Hapus Akun</h3>
      <p class="muted">Setelah akun dihapus, semua data akun akan hilang permanen. Masukkan kata sandi untuk mengonfirmasi.</p>
      @error('password', 'userDeletion')<div class="alert error">{{ $message }}</div>@enderror
      <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf @method('DELETE')
        <div class="field"><label for="delete_password">Kata Sandi</label><input id="delete_password" type="password" name="password" placeholder="Kata sandi Anda"></div>
        <button class="btn" type="submit" style="background:#b3261e;border-color:#b3261e;color:#fff" onclick="return confirm('Hapus akun secara permanen?')">Hapus Akun</button>
      </form>
    </section>
  </div>
</x-app-layout>
