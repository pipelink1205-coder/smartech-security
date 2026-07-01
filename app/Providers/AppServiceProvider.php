<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // En producción tras IIS/proxy, el servidor puede no reportar HTTPS a PHP.
        // Si APP_URL es https, forzamos el esquema para que Livewire y los assets
        // no generen URLs http (evita el bloqueo por "mixed content").
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
