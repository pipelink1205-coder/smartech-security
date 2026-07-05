<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Servicios del sitio (11 base + los que exportes desde admin).
     * Idempotente: updateOrCreate por slug — seguro en local y producción.
     *
     *   php artisan db:seed --class=ServiceSeeder
     *
     * Tras editar en Filament y querer versionar:
     *   php artisan services:export-seeder --exclude=ffff
     */
    public function run(): void
    {
        $services = require __DIR__ . '/data/services.php';

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                array_merge(['is_active' => true], $service),
            );
        }
    }
}
