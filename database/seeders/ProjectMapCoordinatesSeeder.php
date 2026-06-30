<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Rellena coordenadas del mapa en proyectos existentes (por slug).
 * Seguro en producción: no borra ni crea registros.
 *
 * php artisan db:seed --class=ProjectMapCoordinatesSeeder
 */
class ProjectMapCoordinatesSeeder extends Seeder
{
    /** @var array<string, array{latitude: float, longitude: float, comuna_numero: ?int, address: string}> */
    protected array $coordinates = [
        'edificio-torre-ejecutiva' => [
            'latitude' => 6.2087,
            'longitude' => -75.5671,
            'comuna_numero' => 14,
            'address' => 'Calle 10 # 43-30, El Poblado, Medellín',
        ],
        'hotel-boutique' => [
            'latitude' => 6.2095,
            'longitude' => -75.5682,
            'comuna_numero' => 14,
            'address' => 'Carrera 37 # 8A-50, El Poblado, Medellín',
        ],
        'conjunto-altos-envigado' => [
            'latitude' => 6.1759,
            'longitude' => -75.5917,
            'comuna_numero' => null,
            'address' => 'Calle 39 Sur # 27-90, Envigado',
        ],
        'centro-comercial-mayorca' => [
            'latitude' => 6.1847,
            'longitude' => -75.5991,
            'comuna_numero' => null,
            'address' => 'Carrera 50 # 38-55, Itagüí',
        ],
        'textiles-medellin' => [
            'latitude' => 6.3373,
            'longitude' => -75.5579,
            'comuna_numero' => null,
            'address' => 'Autopista Norte Km 14, Bello',
        ],
        'apartamento-laureles' => [
            'latitude' => 6.24915,
            'longitude' => -75.59085,
            'comuna_numero' => 10,
            'address' => 'Carrera 72 # 11-11, Medellín',
        ],
    ];

    public function run(): void
    {
        foreach ($this->coordinates as $slug => $data) {
            $updated = Project::query()
                ->where('slug', $slug)
                ->update($data);

            if ($updated) {
                $this->command?->info("Coordenadas actualizadas: {$slug}");
            }
        }

        $withCoords = Project::query()->onMap()->count();
        $this->command?->info("Proyectos con pin en mapa: {$withCoords}");
    }
}
