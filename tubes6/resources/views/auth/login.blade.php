@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <h1 class="h3 fw-bold mb-1">Login</h1>
        <p class="text-muted mb-4">Masuk untuk membuka dashboard ParkTrack.</p>

        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0">
            Belum mempunyai akun?
            <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection
