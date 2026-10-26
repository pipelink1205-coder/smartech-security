<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Servicios del sitio y servicios adicionales exportados desde admin.
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
            $existing = Service::query()->where('slug', $service['slug'])->first();

            if ($existing) {
                // En producción las fotos y la visibilidad se editan en Filament.
                // El seeder actualiza textos/datos, no pisa image ni los toggles.
                unset($service['image'], $service['is_active'], $service['show_on_home']);
                $existing->fill($service)->save();
                continue;
            }

            Service::query()->create(array_merge([
                'is_active' => true,
                'show_on_home' => true,
            ], $service));
        }
    }
}
