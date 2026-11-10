<?php

use Illuminate\Support\Facades\Route;
use Modules\Kategori\Controllers\KategoriController;

Route::middleware(['auth', 'role:petugas'])
    ->prefix('kategori')
    ->name('kategori.')
    ->group(function (): void {
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::get('/create', [KategoriController::class, 'create'])->name('create');
        Route::post('/', [KategoriController::class, 'store'])->name('store');
        Route::get('/{kategori}/edit', [KategoriController::class, 'edit'])->name('edit');
        Route::put('/{kategori}', [KategoriController::class, 'update'])->name('update');
        Route::delete('/{kategori}', [KategoriController::class, 'destroy'])->name('destroy');
    });
