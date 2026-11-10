@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-light p-3">
            <h3 class="fw-bold text-primary mb-1">Selamat Datang di ParkTrack</h3>
            <p class="text-muted mb-0">Sistem Manajemen Reservasi Parkir Kampus / Gedung Terpadu</p>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-bold">Total Kategori Parkir</h6>
                <h2 class="fw-bold mb-0 text-primary">{{ $totalKategori ?? 0 }}</h2>
                <small class="text-muted">Kategori aktif di sistem</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase fw-bold">Total Reservasi Terdaftar</h6>
                <h2 class="fw-bold mb-0 text-success">{{ $totalReservasi ?? 0 }}</h2>
                <small class="text-muted">Data reservasi tersimpan di database</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">5 Reservasi Terbaru</h5>
        <a href="{{ route('denda.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Pengguna</th>
                        <th>Nomor Plat</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservasiTerbaru ?? [] as $res)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $res->nama_pengguna }}</td>
                            <td><span class="badge bg-dark">{{ $res->nomor_plat }}</span></td>
                            <td>{{ $res->kategori->nama_kategori ?? 'Umum' }}</td>
                            <td>{{ $res->tanggal_reservasi }}</td>
                            <td>
                                @if($res->status == 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($res->status == 'selesai')
                                    <span class="badge bg-secondary">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Belum ada data reservasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection