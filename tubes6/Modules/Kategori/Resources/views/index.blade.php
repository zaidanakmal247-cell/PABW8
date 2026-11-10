@extends('layouts.app')

@section('title', 'Kategori Parkir')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Kategori Parkir</h1>
        <p class="text-muted mb-0">CRUD sinkron untuk pengelolaan area parkir.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('kategori.create') }}">Tambah kategori</a>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nama kategori</th><th>Deskripsi</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($kategoris as $kategori)
                    <tr>
                        <td class="fw-semibold">{{ $kategori->nama_kategori }}</td>
                        <td>{{ $kategori->deskripsi ?: '—' }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('kategori.edit', $kategori) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('kategori.destroy', $kategori) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td class="text-center text-muted py-4" colspan="3">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
