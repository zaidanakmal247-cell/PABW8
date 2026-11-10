<?php

use App\Providers\AppServiceProvider;
use Modules\Kategori\Providers\KategoriServiceProvider;
use Modules\Reservasi\Providers\ReservasiServiceProvider;

return [
    AppServiceProvider::class,
    KategoriServiceProvider::class,
    ReservasiServiceProvider::class,
];