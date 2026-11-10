@extends('layouts.app')

@section('title', $managedUser->exists ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('content')
<div class="page-heading"><div><p class="eyebrow">ADMINISTRASI</p><h1>{{ $managedUser->exists ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h1></div></div>
<form class="card form-grid" method="POST" action="{{ $action }}">
    @csrf @if($method !== 'POST')@method($method)@endif
    <div class="field"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', $managedUser->name) }}" required>@error('name')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email', $managedUser->email) }}" required>@error('email')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="role">Role</label>
        @if($managedUser->exists && auth()->user()->is($managedUser))
            <input type="hidden" name="role" value="admin"><select id="role" disabled><option selected>Admin</option></select>
        @else
            <select id="role" name="role" required><option value="user" @selected(old('role', $managedUser->role ?: 'user') === 'user')>User</option><option value="admin" @selected(old('role', $managedUser->role) === 'admin')>Admin</option></select>
        @endif
        @error('role')<small class="error">{{ $message }}</small>@enderror
    </div>
    <div class="field"><label for="password">Password {{ $managedUser->exists ? '(kosongkan jika tidak diubah)' : '' }}</label><input id="password" name="password" type="password" autocomplete="new-password" @required(!$managedUser->exists)>@error('password')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="password_confirmation">Konfirmasi password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @required(!$managedUser->exists)></div>
    <div class="form-actions"><button class="button" type="submit">Simpan</button><a class="button quiet" href="{{ route('users.index') }}">Batal</a></div>
</form>
@endsection
