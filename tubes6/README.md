# ParkTrack

## Autentikasi

Autentikasi dibuat secara manual menggunakan fitur session dan guard bawaan Laravel, bukan Laravel Breeze. Pilihan ini sesuai untuk kebutuhan aplikasi yang hanya memerlukan register, login, dan logout sederhana; validasi serta alur setelah autentikasi dapat disesuaikan langsung tanpa memasang starter kit tambahan. Password disimpan sebagai hash melalui cast `hashed` pada model `User`.

Semua halaman pengelolaan dan endpoint data dilindungi middleware `auth`. Halaman login (`/login`) dan register (`/register`) hanya dapat diakses oleh pengguna yang belum login. Setelah register atau login berhasil, pengguna diarahkan ke dashboard. Registrasi publik selalu menghasilkan role `user`.

## Modul ParkTrack (HMVC)

Fitur dibagi ke dalam dua modul mandiri di `Modules/`, masing-masing dengan model, controller, route, provider, dan view:

- **Kategori** (`/kategori`) menyediakan CRUD sinkron melalui formulir dan redirect. Modul ini hanya dapat dikelola oleh `petugas`.
- **Reservasi** (`/reservasi`) menyediakan CRUD AJAX berbasis JSON. `user` dapat membuat dan mengubah reservasi miliknya sendiri; status dan denda hanya dapat diubah oleh `petugas`, yang dapat melihat seluruh reservasi. Denda menyimpan nominal dan keterangan.
- **Denda** (`/denda`) menampilkan denda yang sudah dicatat petugas. Petugas melihat seluruh denda; user hanya melihat denda pada reservasi miliknya.

Reservasi mendukung unggahan STNK JPG, PNG, atau PDF hingga 4 MB. Berkas disimpan di `storage/app/private` dan dibuka kembali melalui endpoint yang tetap dilindungi login. Data contoh menyediakan enam kategori dan sepuluh reservasi agar modul dapat langsung didemonstrasikan.

Untuk pengujian/manual demo, jalankan `php artisan migrate --seed`. Akun demo user dan petugas tersedia di tabel berikut.

## Akun dummy

Untuk database baru, jalankan `php artisan migrate --seed`. Jika tabel sudah tersedia dan hanya ingin menambahkan akun, jalankan `php artisan db:seed --class=UserSeeder`. Seeder mendaftarkan akun user dan petugas tetap serta empat akun Faker (total enam akun).

| Peran | Email | Password |
|---|---|---|
| User demo | `user@parktrack.test` | `password` |
| Petugas demo | `petugas@parktrack.test` | `password` |
| Empat akun dummy | Email Faker tersimpan di tabel `users` | `password` |

Untuk memeriksa akun menggunakan phpMyAdmin, buka database yang dipakai aplikasi lalu pilih tabel `users`. Kolom `password` berisi hash bcrypt, bukan password teks biasa. Jalankan aplikasi dengan koneksi MySQL/MariaDB pada berkas `.env` agar database tersebut tampil di phpMyAdmin.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
