<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Modules\Kategori\Models\Kategori;
use Modules\Reservasi\Models\Reservasi;

class TubesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Data Kategori (6 item, aman dijalankan ulang)
        $daftar = [
            'Parkir VIP' => 'Area parkir dekat pintu masuk dengan pengawasan CCTV 24 jam.',
            'Parkir Reguler' => 'Area parkir umum standar gedung.',
            'Parkir Motor' => 'Area khusus sepeda motor beratap.',
            'Parkir Disabilitas' => 'Area dekat lift untuk pengguna difabel.',
            'Parkir Karyawan' => 'Area parkir khusus karyawan.',
            'Parkir Tamu' => 'Area parkir sementara untuk tamu.',
        ];

        $kategoriIds = [];
        foreach ($daftar as $nama => $deskripsi) {
            $kategoriIds[] = Kategori::updateOrCreate(
                ['nama_kategori' => $nama],
                ['deskripsi' => $deskripsi]
            )->id;
        }

        // 2. Minimal 10 data dummy Reservasi (hanya jika tabel masih kosong)
        if (Reservasi::count() === 0) {
            for ($i = 0; $i < 10; $i++) {
                Reservasi::create([
                    'kategori_id' => $faker->randomElement($kategoriIds),
                    'user_id' => User::where('email', 'user@parktrack.test')->value('id'),
                    'nama_pengguna' => $faker->name,
                    'nomor_plat' => 'D '.$faker->numberBetween(1000, 9999).' '.strtoupper($faker->lexify('??')),
                    'tanggal_reservasi' => $faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
                    'durasi_jam' => $faker->numberBetween(1, 12),
                    'status' => $faker->randomElement(['pending', 'disetujui', 'selesai']),
                ]);
            }
        }
    }
}
