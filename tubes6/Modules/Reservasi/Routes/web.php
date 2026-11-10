<?php

use Illuminate\Support\Facades\Route;
use Modules\Reservasi\Controllers\ReservasiController;

Route::middleware(['auth', 'role:user,petugas'])
    ->prefix('reservasi')
    ->name('reservasi.')
    ->group(function (): void {
        Route::get('/', [ReservasiController::class, 'index'])->name('index');
        Route::get('/data', [ReservasiController::class, 'data'])->name('data');
        Route::post('/', [ReservasiController::class, 'store'])
            ->middleware('role:user')
            ->name('store');
        Route::put('/{reservasi}', [ReservasiController::class, 'update'])->name('update');
        Route::get('/{reservasi}/stnk', [ReservasiController::class, 'stnk'])->name('stnk');
    });
