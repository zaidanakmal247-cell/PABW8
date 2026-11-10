@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="mb-4">
    <h1 class="h3">Tambah Kategori Parkir</h1>
    <p class="text-muted">Perubahan disimpan setelah formulir dikirim.</p>
</div>
@include('kategori::form', ['action' => route('kategori.store'), 'method' => 'POST'])
@endsection
