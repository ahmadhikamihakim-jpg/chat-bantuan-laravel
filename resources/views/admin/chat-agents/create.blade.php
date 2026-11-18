@extends('layouts.admin')
@section('title', 'Tambah Tim WhatsApp')
@section('content')
<div class="card">
<h3>Tambah Tim / Sales WhatsApp</h3>
<form method="POST" action="{{ route('admin.chat-agents.store') }}">
@csrf
@include('admin.chat-agents._form')
<div style="margin-top:22px"><button class="btn gold" type="submit">Simpan Tim</button> <a class="btn" href="{{ route('admin.chat-agents.index') }}">Batal</a></div>
</form>
</div>
@endsection
