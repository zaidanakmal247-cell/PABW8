@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <h1 class="h3 fw-bold mb-1">Buat akun</h1>
        <p class="text-muted mb-4">Daftar untuk menggunakan ParkTrack.</p>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" autocomplete="name" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Password minimal 8 karakter.</div>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0">
            Sudah mempunyai akun?
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>
@endsection
