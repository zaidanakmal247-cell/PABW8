<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected from protected pages to login', function () {
    $this->get('/')->assertRedirect(route('login'));
    $this->get('/dashboard')->assertRedirect(route('login'));
    $this->get('/reservasi')->assertRedirect(route('login'));
    $this->post('/reservasi')->assertRedirect(route('login'));
    $this->get('/denda')->assertRedirect(route('login'));
});

test('login and registration pages are available to guests', function () {
    $this->get(route('login'))->assertOk()->assertSee('Login');
    $this->get(route('register'))->assertOk()->assertSee('Buat akun');
});

test('a user can register and is redirected to the dashboard', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Pengguna Baru',
        'email' => 'baru@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => User::ROLE_PETUGAS,
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'baru@example.test']);
    $this->assertDatabaseHas('users', ['email' => 'baru@example.test', 'role' => User::ROLE_USER]);
    expect(password_verify('password123', User::where('email', 'baru@example.test')->value('password')))->toBeTrue();
});

test('an authenticated user can view the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Selamat Datang di ParkTrack')
        ->assertSee('Logout');
});

test('registration displays clear validation errors', function () {
    $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('a user can log in and is redirected to the dashboard', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('invalid login credentials show an error', function () {
    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'unknown@example.test',
            'password' => 'incorrect',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('a logged-in user can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('the user seeder creates a user account, a petugas account, and four faker accounts', function () {
    $this->seed(UserSeeder::class);

    expect(User::count())->toBe(6);
    expect(User::where('email', 'petugas@parktrack.test')->exists())->toBeTrue();
    expect(User::where('email', 'user@parktrack.test')->exists())->toBeTrue();
    expect(password_verify('password', User::where('email', 'petugas@parktrack.test')->value('password')))->toBeTrue();
    expect(User::where('email', 'petugas@parktrack.test')->value('role'))->toBe(User::ROLE_PETUGAS);
    expect(User::where('email', 'user@parktrack.test')->value('role'))->toBe(User::ROLE_USER);
});
