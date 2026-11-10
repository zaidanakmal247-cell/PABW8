<?php

namespace Modules\Reservasi\Providers;

use Illuminate\Support\ServiceProvider;

class ReservasiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'reservasi');
    }
}
