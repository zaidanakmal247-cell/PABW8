@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="auth-shell">
    <form class="card auth-card" method="POST" action="{{ route('login') }}">
        @csrf
        <p class="eyebrow">SELAMAT DATANG</p><h1>Masuk ke SiPustaka</h1><p class="muted">Gunakan akun anggota atau petugas perpustakaan.</p>
        @if(session('success'))<div class="notice success" style="margin-top:16px">{{ session('success') }}</div>@endif
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required>@error('email')<small class="error">{{ $message }}</small>@enderror</div>
        <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>@error('password')<small class="error">{{ $message }}</small>@enderror</div>
        <label style="display:flex;align-items:center;gap:8px;margin:12px 0 18px;font-size:13px"><input type="checkbox" name="remember" value="1" style="width:auto;min-height:auto"> Ingat saya</label>
        <button class="button" type="submit">Masuk</button>
    </form>
    <p class="auth-footer">Belum punya akun? <a href="{{ route('register') }}">Daftar anggota</a></p>
</div>
@endsection
