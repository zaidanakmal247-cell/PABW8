<?php

namespace Modules\Kategori\Providers;

use Illuminate\Support\ServiceProvider;

class KategoriServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'kategori');
    }
}
