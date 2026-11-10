@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="page-heading"><div><p class="eyebrow">ADMINISTRASI</p><h1>Manajemen Pengguna</h1><p class="muted">Kelola akun anggota dan petugas.</p></div><a class="button" href="{{ route('users.create') }}">Tambah pengguna</a></div>
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
@if($errors->has('user'))<div class="notice alert-error">{{ $errors->first('user') }}</div>@endif
<div class="table-wrap">
<table>
    <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($users as $user)
        <tr><td><strong>{{ $user->name }}</strong>@if(auth()->user()->is($user)) <span class="muted">(Anda)</span>@endif</td><td>{{ $user->email }}</td><td>{{ ucfirst($user->role) }}</td>
            <td class="actions"><a href="{{ route('users.edit', $user) }}">Ubah</a>
                @unless(auth()->user()->is($user))
                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">@csrf @method('DELETE')<button class="link danger-text" type="submit">Hapus</button></form>
                @endunless
            </td>
        </tr>
    @empty
        <tr><td class="empty" colspan="4">Belum ada pengguna.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="pagination">{{ $users->links() }}</div>
@endsection
