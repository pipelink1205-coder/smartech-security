<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class BrandLogosSeeder extends Seeder
{
    /**
     * Descarga logos de marcas a public/img/brands/ (idempotente).
     * Ejecutar tras clonar el repo o al agregar marcas en config/brands.php:
     *
     *   php artisan db:seed --class=BrandLogosSeeder
     *   php artisan brands:sync --force
     */
    public function run(): void
    {
        Artisan::call('brands:sync', [], $this->command?->getOutput());
    }
}
