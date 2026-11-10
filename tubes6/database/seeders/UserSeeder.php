<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = User::updateOrCreate(
            ['email' => 'petugas@parktrack.test'],
            ['name' => 'Petugas ParkTrack', 'password' => 'password']
        );
        $petugas->forceFill(['role' => User::ROLE_PETUGAS])->save();

        $user = User::updateOrCreate(
            ['email' => 'user@parktrack.test'],
            ['name' => 'Pengguna ParkTrack', 'password' => 'password']
        );
        $user->forceFill(['role' => User::ROLE_USER])->save();

        // Keep six demo accounts when seeding an empty database.
        User::factory()->count(max(0, 6 - User::count()))->create();
    }
}
