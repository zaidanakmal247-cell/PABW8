<?php

use App\Models\User;
use Database\Seeders\TubesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Kategori\Models\Kategori;
use Modules\Reservasi\Models\Reservasi;

uses(RefreshDatabase::class);

function userWithRole(string $role): User
{
    $user = User::factory()->create();
    $user->forceFill(['role' => $role])->save();

    return $user;
}

function makeReservasi(User $owner, Kategori $kategori, array $attributes = []): Reservasi
{
    return Reservasi::create([
        'user_id' => $owner->id,
        'kategori_id' => $kategori->id,
        'nama_pengguna' => $owner->name,
        'nomor_plat' => 'D 1234 AB',
        'tanggal_reservasi' => '2026-11-10',
        'durasi_jam' => 2,
        'status' => 'pending',
        ...$attributes,
    ]);
}

test('all management, fine, and file routes require authentication', function () {
    $this->get(route('kategori.index'))->assertRedirect(route('login'));
    $this->get(route('reservasi.index'))->assertRedirect(route('login'));
    $this->get(route('reservasi.data'))->assertRedirect(route('login'));
    $this->get(route('reservasi.stnk', 1))->assertRedirect(route('login'));
    $this->post(route('reservasi.store'))->assertRedirect(route('login'));
    $this->get(route('denda.index'))->assertRedirect(route('login'));
});

test('only petugas can manage categories synchronously', function () {
    $this->seed(TubesSeeder::class);
    expect(Kategori::count())->toBeGreaterThanOrEqual(5);

    $this->actingAs(userWithRole(User::ROLE_USER))
        ->get(route('kategori.index'))
        ->assertForbidden();

    $this->actingAs(userWithRole(User::ROLE_PETUGAS))
        ->get(route('kategori.index'))
        ->assertOk()
        ->assertSee('CRUD sinkron');

    $this->post(route('kategori.store'), [
        'nama_kategori' => 'Parkir Tamu Khusus',
        'deskripsi' => 'Area khusus tamu.',
    ])->assertRedirect(route('kategori.index'));

    $kategori = Kategori::where('nama_kategori', 'Parkir Tamu Khusus')->firstOrFail();
    $this->put(route('kategori.update', $kategori), [
        'nama_kategori' => 'Parkir Tamu VIP',
        'deskripsi' => 'Area khusus tamu VIP.',
    ])->assertRedirect(route('kategori.index'));

    $this->delete(route('kategori.destroy', $kategori))->assertRedirect(route('kategori.index'));
    $this->assertDatabaseMissing('kategori', ['id' => $kategori->id]);
});

test('users can create and edit only their own reservation without changing its status', function () {
    Storage::fake('local');
    $this->seed(TubesSeeder::class);
    $user = userWithRole(User::ROLE_USER);
    $kategori = Kategori::firstOrFail();
    $otherUser = userWithRole(User::ROLE_USER);
    $otherReservation = makeReservasi($otherUser, $kategori, [
        'denda_nominal' => 40000,
        'denda_keterangan' => 'Parkir melewati batas waktu.',
    ]);

    $this->actingAs($user)->get(route('reservasi.index'))
        ->assertOk()
        ->assertSee('Buat dan ubah reservasi milik Anda');

    $this->postJson(route('reservasi.store'), [
        'kategori_id' => $kategori->id,
        'nama_pengguna' => $user->name,
        'nomor_plat' => 'D 1234 AB',
        'tanggal_reservasi' => '2026-11-10',
        'durasi_jam' => 3,
        'status' => 'disetujui',
        'denda_nominal' => 90000,
        'file_stnk' => UploadedFile::fake()->createWithContent('stnk.pdf', "%PDF-1.4\nDemo STNK\n"),
    ])->assertCreated()
        ->assertJsonPath('data.status', 'pending');

    $reservation = Reservasi::where('user_id', $user->id)->firstOrFail();
    Storage::disk('local')->assertExists($reservation->file_stnk);
    $this->get(route('reservasi.stnk', $reservation))->assertOk();
    $this->getJson(route('reservasi.data'))
        ->assertOk()
        ->assertJsonFragment(['id' => $reservation->id])
        ->assertJsonMissing(['id' => $otherReservation->id]);

    $this->putJson(route('reservasi.update', $reservation), [
        'kategori_id' => $kategori->id,
        'nama_pengguna' => 'Nama Diperbarui',
        'nomor_plat' => 'D 5678 CD',
        'tanggal_reservasi' => '2026-11-11',
        'durasi_jam' => 4,
        'status' => 'selesai',
        'denda_nominal' => 1,
    ])->assertOk()->assertJsonPath('data.status', 'pending');

    $this->putJson(route('reservasi.update', $otherReservation), [
        'kategori_id' => $kategori->id,
        'nama_pengguna' => 'Tidak berhak',
        'nomor_plat' => 'D 9999 ZZ',
        'tanggal_reservasi' => '2026-11-11',
        'durasi_jam' => 1,
    ])->assertForbidden();

    $this->get(route('reservasi.stnk', $otherReservation))->assertForbidden();
    $this->assertDatabaseHas('reservasi', [
        'id' => $reservation->id,
        'nama_pengguna' => 'Nama Diperbarui',
        'status' => 'pending',
        'denda_nominal' => null,
    ]);
});

test('petugas can review all reservations, update status and add a fine but cannot create reservations', function () {
    Storage::fake('local');
    $this->seed(TubesSeeder::class);
    $owner = userWithRole(User::ROLE_USER);
    $kategori = Kategori::firstOrFail();
    $filePath = 'reservasi/stnk/reservation.pdf';
    Storage::disk('local')->put($filePath, "%PDF-1.4\nSTNK\n");
    $reservation = makeReservasi($owner, $kategori, ['file_stnk' => $filePath]);
    $petugas = userWithRole(User::ROLE_PETUGAS);

    $this->actingAs($petugas)->get(route('reservasi.index'))
        ->assertOk()
        ->assertSee('Perbarui status reservasi dan catat denda');
    $this->postJson(route('reservasi.store'), [])->assertForbidden();
    $this->getJson(route('reservasi.data'))
        ->assertOk()
        ->assertJsonFragment(['id' => $reservation->id]);

    $this->putJson(route('reservasi.update', $reservation), [
        'status' => 'disetujui',
        'denda_nominal' => 75000,
        'denda_keterangan' => 'Parkir melebihi waktu reservasi.',
        'nama_pengguna' => 'Tidak boleh diubah petugas',
    ])->assertOk()
        ->assertJsonPath('data.status', 'disetujui')
        ->assertJsonPath('data.nama_pengguna', $owner->name)
        ->assertJsonPath('data.denda_nominal', '75000.00');

    $this->get(route('reservasi.stnk', $reservation))->assertOk();
    $this->postJson(route('reservasi.store'))->assertForbidden();
    $this->assertDatabaseHas('reservasi', [
        'id' => $reservation->id,
        'status' => 'disetujui',
        'denda_nominal' => 75000,
    ]);
});

test('users see only their own fines and petugas can see all recorded fines', function () {
    $this->seed(TubesSeeder::class);
    $kategori = Kategori::firstOrFail();
    $user = userWithRole(User::ROLE_USER);
    $otherUser = userWithRole(User::ROLE_USER);
    makeReservasi($user, $kategori, [
        'denda_nominal' => 50000,
        'denda_keterangan' => 'Melebihi waktu parkir.',
    ]);
    makeReservasi($otherUser, $kategori, [
        'denda_nominal' => 80000,
        'denda_keterangan' => 'Parkir di area terlarang.',
    ]);

    $this->actingAs($user)->get(route('denda.index'))
        ->assertOk()
        ->assertSee('Melebihi waktu parkir.')
        ->assertDontSee('Parkir di area terlarang.');

    $this->actingAs(userWithRole(User::ROLE_PETUGAS))->get(route('denda.index'))
        ->assertOk()
        ->assertSee('Melebihi waktu parkir.')
        ->assertSee('Parkir di area terlarang.');
});

test('invalid STNK file types are rejected by the AJAX endpoint', function () {
    $this->seed(TubesSeeder::class);
    $user = userWithRole(User::ROLE_USER);
    $this->actingAs($user)
        ->postJson(route('reservasi.store'), [
            'kategori_id' => Kategori::firstOrFail()->id,
            'nama_pengguna' => $user->name,
            'nomor_plat' => 'D 1234 EF',
            'tanggal_reservasi' => '2026-11-10',
            'durasi_jam' => 1,
            'file_stnk' => UploadedFile::fake()->create('script.exe', 10),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('file_stnk');
});

test('legacy public STNK files are moved to private storage when opened', function () {
    Storage::fake('local');
    Storage::fake('public');
    $this->seed(TubesSeeder::class);
    $owner = userWithRole(User::ROLE_USER);
    $reservasi = makeReservasi($owner, Kategori::firstOrFail());
    $legacyPath = 'stnk/legacy-stnk.jpg';
    Storage::disk('public')->put($legacyPath, 'legacy STNK content');
    $reservasi->update(['file_stnk' => $legacyPath]);

    $this->actingAs(userWithRole(User::ROLE_PETUGAS))
        ->get(route('reservasi.stnk', $reservasi))
        ->assertOk();

    $reservasi->refresh();
    expect($reservasi->file_stnk)->toStartWith('reservasi/stnk/');
    Storage::disk('local')->assertExists($reservasi->file_stnk);
    Storage::disk('public')->assertMissing($legacyPath);
});
