<?php

namespace Modules\Buku\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Buku\Models\Buku;

class BukuDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['judul' => 'Laskar Pelangi', 'penulis' => 'Andrea Hirata', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => 2005, 'stok' => 8],
            ['judul' => 'Bumi Manusia', 'penulis' => 'Pramoedya Ananta Toer', 'penerbit' => 'Hasta Mitra', 'tahun_terbit' => 1980, 'stok' => 5],
            ['judul' => 'Negeri 5 Menara', 'penulis' => 'Ahmad Fuadi', 'penerbit' => 'Gramedia Pustaka Utama', 'tahun_terbit' => 2009, 'stok' => 6],
            ['judul' => 'Perahu Kertas', 'penulis' => 'Dee Lestari', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => 2009, 'stok' => 4],
            ['judul' => 'Ronggeng Dukuh Paruk', 'penulis' => 'Ahmad Tohari', 'penerbit' => 'Gramedia Pustaka Utama', 'tahun_terbit' => 1982, 'stok' => 3],
        ];

        foreach ($books as $book) {
            Buku::updateOrCreate(['judul' => $book['judul']], $book);
        }
    }
}
