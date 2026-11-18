@extends('layouts.admin')
@section('title', 'Ubah Tim WhatsApp')
@section('content')
<div class="card">
<h3>Ubah Tim: {{ $agent->name }}</h3>
<form method="POST" action="{{ route('admin.chat-agents.update', $agent) }}">
@csrf @method('PUT')
@include('admin.chat-agents._form')
<div style="margin-top:22px"><button class="btn gold" type="submit">Simpan Perubahan</button> <a class="btn" href="{{ route('admin.chat-agents.index') }}">Batal</a></div>
</form>
</div>
@endsection
