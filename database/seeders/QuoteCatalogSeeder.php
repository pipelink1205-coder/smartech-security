<?php

namespace Database\Seeders;

use App\Models\QuoteCatalogItem;
use Illuminate\Database\Seeder;

class QuoteCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['type' => 'product', 'code' => 'CABLE', 'name' => 'Cable UTP', 'description' => 'Cable UTP categoría 6', 'unit' => 'metro', 'default_unit_price' => 60000, 'default_tax_rate' => 19],
            ['type' => 'product', 'code' => 'TUBERIA EMT', 'name' => 'Tubería EMT', 'description' => 'Tubería EMT y accesorios', 'unit' => 'metro', 'default_unit_price' => 80000, 'default_tax_rate' => 19],
            ['type' => 'product', 'code' => 'CANALETA PLASTICA', 'name' => 'Canaleta plástica', 'description' => 'Canaleta plástica para conducción de cableado', 'unit' => 'metro', 'default_unit_price' => 12000, 'default_tax_rate' => 19],
            ['type' => 'product', 'code' => 'FACE PLATE', 'name' => 'Punto de red', 'description' => 'Caja de sobreponer, face plate, jack y patch cord', 'unit' => 'punto', 'default_unit_price' => 55000, 'default_tax_rate' => 19],
            ['type' => 'service', 'code' => 'MANO DE OBRA', 'name' => 'Instalación y configuración', 'description' => 'Servicio profesional de instalación y configuración', 'unit' => 'servicio', 'default_unit_price' => 260000, 'default_tax_rate' => 19],
            ['type' => 'product', 'code' => 'CAMARA IP', 'name' => 'Cámara IP', 'description' => 'Cámara IP 4K', 'unit' => 'unidad', 'default_unit_price' => 0, 'default_tax_rate' => 19],
            ['type' => 'product', 'code' => 'NVR', 'name' => 'Grabador NVR', 'description' => 'Grabador de video en red NVR', 'unit' => 'unidad', 'default_unit_price' => 0, 'default_tax_rate' => 19],
            ['type' => 'service', 'code' => 'VISITA', 'name' => 'Visita técnica', 'description' => 'Visita técnica y diagnóstico', 'unit' => 'servicio', 'default_unit_price' => 0, 'default_tax_rate' => 0],
        ];

        foreach ($items as $item) {
            QuoteCatalogItem::updateOrCreate(
                ['code' => $item['code']],
                [...$item, 'is_active' => true],
            );
        }
    }
}
