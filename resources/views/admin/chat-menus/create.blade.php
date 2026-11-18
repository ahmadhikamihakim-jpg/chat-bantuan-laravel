@extends('layouts.admin')
@section('title', 'Tambah Menu Chat')
@section('content')
<div class="card">
<h3>Tambah Menu Chat</h3>
<form method="POST" action="{{ route('admin.chat-menus.store') }}">
@csrf
@include('admin.chat-menus._form')
<div style="margin-top:22px"><button class="btn gold" type="submit">Simpan Menu</button> <a class="btn" href="{{ route('admin.chat-menus.index') }}">Batal</a></div>
</form>
</div>
@endsection
