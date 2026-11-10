@extends('layouts.app')

@section('title', $buku->exists ? 'Ubah Buku' : 'Tambah Buku')

@section('content')
<div class="page-heading"><div><p class="eyebrow">KATALOG</p><h1>{{ $buku->exists ? 'Ubah Buku' : 'Tambah Buku' }}</h1></div></div>
<form class="card form-grid" method="POST" action="{{ $action }}">
    @csrf @if($method !== 'POST')@method($method)@endif
    <div class="field"><label for="judul">Judul</label><input id="judul" name="judul" value="{{ old('judul', $buku->judul) }}" required>@error('judul')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="penulis">Penulis</label><input id="penulis" name="penulis" value="{{ old('penulis', $buku->penulis) }}" required>@error('penulis')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="penerbit">Penerbit</label><input id="penerbit" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" required>@error('penerbit')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="tahun_terbit">Tahun terbit</label><input id="tahun_terbit" type="number" min="1000" max="2100" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required>@error('tahun_terbit')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="field"><label for="stok">Stok</label><input id="stok" type="number" min="0" name="stok" value="{{ old('stok', $buku->stok ?? 0) }}" required>@error('stok')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="form-actions"><button class="button" type="submit">Simpan</button><a class="button quiet" href="{{ route('buku.index') }}">Batal</a></div>
</form>
@endsection
