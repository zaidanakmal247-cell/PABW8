<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Buku\Database\Seeders\BukuDatabaseSeeder;
use Modules\Peminjaman\Database\Seeders\PeminjamanDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sipustaka.test'],
            ['name' => 'Petugas Admin', 'password' => 'password', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'user@sipustaka.test'],
            ['name' => 'Anggota Satu', 'password' => 'password', 'role' => 'user']
        );

        $this->call(BukuDatabaseSeeder::class);
        $this->call(PeminjamanDatabaseSeeder::class);
    }
}