# SiPustaka HMVC

SiPustaka is a Laravel 12 campus library application built with `nwidart/laravel-modules`. Authentication lives in the main Laravel app; catalog, loans, and user administration are separate HMVC modules.

## Setup

Requirements: PHP 8.2+, Composer, MySQL, and Node.js/npm if building frontend assets.

1. Create a MySQL database:

   ```sql
   CREATE DATABASE db_sipustaka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Install and configure:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   Set `DB_HOST`, `DB_PORT`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` for the local MySQL server. The example config uses `db_sipustaka`.

3. Run migrations, sample data, storage link, and server:

   ```powershell
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

4. Visit `http://127.0.0.1:8000`.

Seeded login accounts (change their passwords before deployment):

| Role | Email | Password |
|---|---|---|
| Admin | `admin@sipustaka.test` | `password` |
| User | `user@sipustaka.test` | `password` |

Newly registered accounts are always assigned the `user` role.

## Features

- **Buku** (`/buku`): authenticated catalog with search and pagination; admin-only synchronous create, edit, and delete.
- **Peminjaman** (`/peminjaman`): authenticated AJAX create, read, update, and delete for both roles. Evidence files accept JPG, PNG, or PDF up to 2 MB; uploaded paths are stored in `peminjamans.bukti_path` and files live under `storage/app/public/bukti-peminjaman`.
- **Manajemen User** (`/manajemen-user`): admin-only synchronous user CRUD. Admins cannot remove or downgrade their own account.
- **Authorization**: `role:admin` middleware is registered in `bootstrap/app.php`. All data-management routes also require authentication.

## Evidence checklist

- To demonstrate authorization, sign in as the seeded user and open `/manajemen-user`; Laravel returns HTTP 403. The user can open `/buku` but cannot access admin-only book forms.
- To capture AJAX evidence, open DevTools → **Network** → **Fetch/XHR**, then create/edit/delete a loan. The page makes `GET /peminjaman/data`, `POST /peminjaman`, `POST /peminjaman/{id}` with `_method=PUT`, and `DELETE /peminjaman/{id}` requests without a document reload. A validation failure returns HTTP 422 with per-field errors.
- To show file storage, upload a valid proof and inspect `storage/app/public/bukti-peminjaman`. In phpMyAdmin, inspect `db_sipustaka.peminjamans`; the `bukti_path` column contains a relative path such as `bukti-peminjaman/<generated-name>.pdf`.

## Tests

```powershell
php artisan test
```
