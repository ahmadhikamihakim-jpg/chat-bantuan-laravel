@extends('layouts.admin')
@section('title', 'Ubah Menu Chat')
@section('content')
<div class="card">
<h3>Ubah Menu: {{ $menu->label }}</h3>
<form method="POST" action="{{ route('admin.chat-menus.update', $menu) }}">
@csrf @method('PUT')
@include('admin.chat-menus._form')
<div style="margin-top:22px"><button class="btn gold" type="submit">Simpan Perubahan</button> <a class="btn" href="{{ route('admin.chat-menus.index') }}">Batal</a></div>
</form>
</div>
@endsection
