<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SipustakaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_demo_accounts_and_sample_records(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@sipustaka.test', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'user@sipustaka.test', 'role' => 'user']);
        $this->assertDatabaseCount('bukus', 5);
        $this->assertDatabaseCount('peminjamans', 5);
    }

    public function test_registration_always_creates_a_user_role(): void
    {
        $this->post('/register', [
            'name' => 'Anggota Baru',
            'email' => 'baru@example.test',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
            'role' => 'admin',
        ])->assertRedirect('/buku');

        $this->assertDatabaseHas('users', ['email' => 'baru@example.test', 'role' => 'user']);
    }

    public function test_user_can_view_books_but_cannot_access_admin_pages_or_mutations(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/buku')->assertOk();
        $this->actingAs($user)->get('/manajemen-user')->assertForbidden();
        $this->actingAs($user)->post('/buku', [
            'judul' => 'Buku Uji',
            'penulis' => 'Penulis',
            'penerbit' => 'Penerbit',
            'tahun_terbit' => 2020,
            'stok' => 1,
        ])->assertForbidden();
    }

    public function test_admin_can_create_a_book_and_management_pages_require_authentication(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/buku', [
            'judul' => 'Buku Uji',
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'tahun_terbit' => 2020,
            'stok' => 4,
        ])->assertRedirect('/login');

        $this->actingAs($admin)->get('/buku/create')->assertOk()->assertSee('Tambah Buku');
        $this->actingAs($admin)->get('/peminjaman')->assertOk()->assertSee('Data Peminjaman');
        $this->actingAs($admin)->get('/manajemen-user')->assertOk()->assertSee('Manajemen Pengguna');
        $this->actingAs($admin)->get('/manajemen-user/create')->assertOk()->assertSee('Tambah Pengguna');

        $this->actingAs($admin)->post('/buku', [
            'judul' => 'Buku Uji',
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'tahun_terbit' => 2020,
            'stok' => 4,
        ])->assertRedirect(route('buku.index'));

        $book = \Modules\Buku\Models\Buku::query()->where('judul', 'Buku Uji')->firstOrFail();
        $this->assertDatabaseHas('bukus', ['judul' => 'Buku Uji', 'stok' => 4]);
        $this->actingAs($admin)->get('/buku?search=Uji')->assertOk()->assertSee('Buku Uji');
        $this->actingAs($admin)->put('/buku/'.$book->id, [
            'judul' => 'Buku Uji Diubah',
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'tahun_terbit' => 2020,
            'stok' => 2,
        ])->assertRedirect(route('buku.index'));
        $this->assertDatabaseHas('bukus', ['id' => $book->id, 'judul' => 'Buku Uji Diubah', 'stok' => 2]);
        $this->actingAs($admin)->delete('/buku/'.$book->id)->assertRedirect(route('buku.index'));
        $this->assertDatabaseMissing('bukus', ['id' => $book->id]);
    }

    public function test_loan_ajax_validates_uploads_and_removes_files_when_deleted(): void
    {
        Storage::fake('public');
        $member = User::factory()->create(['role' => 'user']);
        $headers = ['Accept' => 'application/json'];

        $this->actingAs($member)->withHeaders($headers)->post('/peminjaman', [
            'nama_peminjam' => '',
        ])->assertUnprocessable()->assertJsonValidationErrors(['nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'status']);

        $this->actingAs($member)->withHeaders($headers)->post('/peminjaman', [
            'nama_peminjam' => 'Anggota Uji',
            'judul_buku' => 'Buku Uji',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_kembali' => '',
            'status' => 'Dipinjam',
            'bukti' => UploadedFile::fake()->createWithContent('bukti.pdf', "%PDF-1.4\n% SiPustaka test bukti\n"),
        ])->assertCreated();

        $this->assertDatabaseHas('peminjamans', ['nama_peminjam' => 'Anggota Uji']);
        $loan = \Modules\Peminjaman\Models\Peminjaman::query()->firstOrFail();
        $path = $loan->bukti_path;
        $this->assertTrue(Storage::disk('public')->exists($path));

        $this->actingAs($member)->withHeaders($headers)->get('/peminjaman/data')
            ->assertOk()
            ->assertJsonPath('data.0.nama_peminjam', 'Anggota Uji')
            ->assertJsonPath('data.0.bukti_url', URL::to('storage/'.$path));

        $this->actingAs($member)->withHeaders($headers)->post('/peminjaman/'.$loan->id, [
            '_method' => 'PUT',
            'nama_peminjam' => 'Anggota Uji',
            'judul_buku' => 'Buku Uji',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_kembali' => '2026-11-05',
            'status' => 'Dikembalikan',
        ])->assertOk()->assertJsonPath('message', 'Data peminjaman berhasil diperbarui.');
        $this->assertSame($path, $loan->fresh()->bukti_path);

        $this->actingAs($member)->withHeaders($headers)->post('/peminjaman/'.$loan->id, [
            '_method' => 'PUT',
            'nama_peminjam' => 'Anggota Uji',
            'judul_buku' => 'Buku Uji',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_kembali' => '2026-11-05',
            'status' => 'Dikembalikan',
            'bukti' => UploadedFile::fake()->createWithContent('bukti-baru.pdf', "%PDF-1.4\n% Bukti baru\n"),
        ])->assertOk();
        $replacementPath = $loan->fresh()->bukti_path;
        $this->assertNotSame($path, $replacementPath);
        $this->assertFalse(Storage::disk('public')->exists($path));
        $this->assertTrue(Storage::disk('public')->exists($replacementPath));

        $this->actingAs($member)->withHeaders($headers)->delete('/peminjaman/'.$loan->id)
            ->assertOk()->assertJsonPath('message', 'Data peminjaman berhasil dihapus.');
        $this->assertFalse(Storage::disk('public')->exists($replacementPath));
        $this->assertDatabaseMissing('peminjamans', ['id' => $loan->id]);
    }

    public function test_admin_cannot_change_or_delete_their_own_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
        ])->assertSessionHasErrors('role');

        $this->actingAs($admin)->delete(route('users.destroy', $admin))->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
    }
}
