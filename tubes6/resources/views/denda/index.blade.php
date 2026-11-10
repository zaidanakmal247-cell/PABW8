@extends('layouts.app')

@section('title', 'Daftar Denda')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Daftar Denda</h1>
    <p class="text-muted mb-0">Denda yang telah dicatat petugas untuk reservasi.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @if(auth()->user()->isPetugas())<th>Pemilik</th>@endif
                    <th>Nama pengguna</th>
                    <th>Nomor plat</th>
                    <th>Tanggal reservasi</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($denda as $item)
                    <tr>
                        @if(auth()->user()->isPetugas())
                            <td>{{ $item->user?->name ?? 'Data lama' }}</td>
                        @endif
                        <td>{{ $item->nama_pengguna }}</td>
                        <td>{{ $item->nomor_plat }}</td>
                        <td>{{ $item->tanggal_reservasi->translatedFormat('d F Y') }}</td>
                        <td class="text-nowrap">Rp {{ number_format((float) $item->denda_nominal, 0, ',', '.') }}</td>
                        <td>{{ $item->denda_keterangan }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center text-muted py-4" colspan="{{ auth()->user()->isPetugas() ? 7 : 6 }}">
                            Belum ada denda yang dicatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
