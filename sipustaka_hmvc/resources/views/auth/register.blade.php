@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
<div class="auth-shell">
    <form class="card auth-card" method="POST" action="{{ route('register') }}">
        @csrf
        <p class="eyebrow">AKUN BARU</p><h1>Daftar anggota</h1><p class="muted">Akun yang didaftarkan memiliki role anggota.</p>
        <div class="field"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<small class="error">{{ $message }}</small>@enderror</div>
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<small class="error">{{ $message }}</small>@enderror</div>
        <div class="field"><label for="password">Password (minimal 8 karakter)</label><input id="password" name="password" type="password" autocomplete="new-password" required>@error('password')<small class="error">{{ $message }}</small>@enderror</div>
        <div class="field"><label for="password_confirmation">Konfirmasi password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
        <button class="button" type="submit">Daftar</button>
    </form>
    <p class="auth-footer">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</div>
@endsection
