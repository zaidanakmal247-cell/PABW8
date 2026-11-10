<?php

namespace Modules\Peminjaman\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Peminjaman\Models\Peminjaman;

class PeminjamanDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $loans = [
            ['nama_peminjam' => 'Anggota Satu', 'judul_buku' => 'Laskar Pelangi', 'tanggal_pinjam' => '2026-10-30', 'tanggal_kembali' => null, 'status' => 'Dipinjam'],
            ['nama_peminjam' => 'Dina Putri', 'judul_buku' => 'Bumi Manusia', 'tanggal_pinjam' => '2026-10-21', 'tanggal_kembali' => '2026-10-28', 'status' => 'Dikembalikan'],
            ['nama_peminjam' => 'Rizky Aditya', 'judul_buku' => 'Negeri 5 Menara', 'tanggal_pinjam' => '2026-11-02', 'tanggal_kembali' => null, 'status' => 'Dipinjam'],
            ['nama_peminjam' => 'Nadia Sari', 'judul_buku' => 'Perahu Kertas', 'tanggal_pinjam' => '2026-10-18', 'tanggal_kembali' => '2026-10-25', 'status' => 'Dikembalikan'],
            ['nama_peminjam' => 'Bima Pratama', 'judul_buku' => 'Ronggeng Dukuh Paruk', 'tanggal_pinjam' => '2026-11-04', 'tanggal_kembali' => null, 'status' => 'Dipinjam'],
        ];

        foreach ($loans as $loan) {
            Peminjaman::firstOrCreate(
                ['nama_peminjam' => $loan['nama_peminjam'], 'judul_buku' => $loan['judul_buku']],
                $loan
            );
        }
    }
}
