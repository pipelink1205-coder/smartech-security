<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Servicios del sitio. Idempotente: updateOrCreate por slug.
     * Contenido en database/seeders/data/services.php
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
