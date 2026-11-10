<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif
            <div class="mb-3">
                <label class="form-label" for="nama_kategori">Nama kategori</label>
                <input class="form-control @error('nama_kategori') is-invalid @enderror" id="nama_kategori" name="nama_kategori" maxlength="100" required value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}">
                @error('nama_kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" maxlength="1000">{{ old('deskripsi', $kategori->deskripsi ?? '') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a class="btn btn-outline-secondary" href="{{ route('kategori.index') }}">Batal</a>
        </form>
    </div>
</div>
@endsection
