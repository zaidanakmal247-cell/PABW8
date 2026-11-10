@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">KATALOG</p><h1>Daftar Buku</h1><p class="muted">Cari dan lihat koleksi perpustakaan.</p></div>
    @if(auth()->user()->role === 'admin')<a class="button" href="{{ route('buku.create') }}">Tambah buku</a>@endif
</div>
@if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
<form class="search" method="GET" action="{{ route('buku.index') }}">
    <label class="sr-only" for="search">Cari buku</label>
    <input id="search" name="search" value="{{ $search }}" placeholder="Cari judul, penulis, atau penerbit">
    <button class="button secondary" type="submit">Cari</button>
    @if($search !== '')<a class="button quiet" href="{{ route('buku.index') }}">Hapus pencarian</a>@endif
</form>
<div class="table-wrap">
<table>
    <thead><tr><th>Judul</th><th>Penulis</th><th>Penerbit</th><th>Tahun</th><th>Stok</th>@if(auth()->user()->role === 'admin')<th>Aksi</th>@endif</tr></thead>
    <tbody>
    @forelse($bukus as $buku)
        <tr>
            <td><strong>{{ $buku->judul }}</strong></td><td>{{ $buku->penulis }}</td><td>{{ $buku->penerbit }}</td><td>{{ $buku->tahun_terbit }}</td><td>{{ $buku->stok }}</td>
            @if(auth()->user()->role === 'admin')
            <td class="actions"><a href="{{ route('buku.edit', $buku) }}">Ubah</a>
                <form method="POST" action="{{ route('buku.destroy', $buku) }}" onsubmit="return confirm('Hapus buku ini?')">@csrf @method('DELETE')<button class="link danger-text" type="submit">Hapus</button></form>
            </td>
            @endif
        </tr>
    @empty
        <tr><td colspan="{{ auth()->user()->role === 'admin' ? 6 : 5 }}" class="empty">Tidak ada buku yang sesuai.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="pagination">{{ $bukus->links() }}</div>
@endsection
