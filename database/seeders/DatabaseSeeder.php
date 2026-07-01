<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ServiceSeeder::class);

        $projects = [
            [
                'slug' => 'edificio-torre-ejecutiva',
                'title' => 'Edificio Torre Ejecutiva',
                'category' => 'Seguridad Corporativa',
                'service_slug' => 'camaras-4k',
                'location' => 'El Poblado',
                'address' => 'Calle 10 # 43-30, El Poblado, Medellín',
                'latitude' => 6.2087,
                'longitude' => -75.5671,
                'comuna_numero' => 14,
                'year' => 2024,
                'is_featured' => true,
                'image' => config('images.projects.edificio-torre-ejecutiva'),
                'description' => 'Instalación de 32 cámaras IP 4K, control de acceso biométrico y red estructurada en El Poblado, Medellín.',
            ],
            [
                'slug' => 'hotel-boutique',
                'title' => 'Hotel Boutique',
                'category' => 'IPTV Hotelera',
                'location' => 'El Poblado',
                'address' => 'Carrera 37 # 8A-50, El Poblado, Medellín',
                'latitude' => 6.2095,
                'longitude' => -75.5682,
                'comuna_numero' => 14,
                'year' => 2024,
                'is_featured' => true,
                'image' => config('images.projects.hotel-boutique'),
                'description' => 'Sistema IPTV con 45 habitaciones, canales internacionales HD y plataforma de Video On Demand.',
            ],
            [
                'slug' => 'conjunto-altos-envigado',
                'title' => 'Conjunto Altos de Envigado',
                'category' => 'Energía Solar',
                'service_slug' => 'energia-solar',
                'location' => 'Envigado',
                'address' => 'Calle 39 Sur # 27-90, Envigado',
                'latitude' => 6.1759,
                'longitude' => -75.5917,
                'comuna_numero' => null,
                'year' => 2025,
                'is_featured' => true,
                'image' => config('images.projects.conjunto-altos-envigado'),
                'description' => 'Sistema fotovoltaico de 15kW para zonas comunes. Reducción del 70% en costos de energía mensuales.',
            ],
            [
                'slug' => 'centro-comercial-mayorca',
                'title' => 'Centro Comercial Mayorca',
                'category' => 'Control de Acceso',
                'service_slug' => 'control-acceso',
                'location' => 'Itagüí',
                'address' => 'Carrera 50 # 38-55, Itagüí',
                'latitude' => 6.1847,
                'longitude' => -75.5991,
                'comuna_numero' => null,
                'year' => 2023,
                'is_featured' => true,
                'image' => config('images.projects.centro-comercial-mayorca'),
                'description' => 'Actualización con torniquetes biométricos y reconocimiento facial 3D.',
            ],
            [
                'slug' => 'textiles-medellin',
                'title' => 'Textiles Medellín S.A.S.',
                'category' => 'Redes Empresariales',
                'service_slug' => 'redes-fibra',
                'location' => 'Bello',
                'address' => 'Autopista Norte Km 14, Bello',
                'latitude' => 6.3373,
                'longitude' => -75.5579,
                'comuna_numero' => null,
                'year' => 2023,
                'is_featured' => true,
                'image' => config('images.projects.textiles-medellin'),
                'description' => 'Cableado Cat6A, fibra óptica y WiFi mesh para 200 usuarios.',
            ],
            [
                'slug' => 'apartamento-laureles',
                'title' => 'Apartamento Inteligente Laureles',
                'category' => 'Domótica',
                'service_slug' => 'domotica',
                'location' => 'La Candelaria',
                'address' => 'Carrera 72 # 11-11, Medellín',
                'latitude' => 6.24915,
                'longitude' => -75.59085,
                'comuna_numero' => 10,
                'year' => 2024,
                'is_featured' => true,
                'image' => config('images.projects.apartamento-laureles'),
                'description' => 'Automatización de iluminación, cortinas, clima y seguridad integrada vía app en vivienda.',
            ],
        ];

        $u = static fn (string $id) =>
            "https://images.unsplash.com/{$id}?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80";

        $galleries = [
            'edificio-torre-ejecutiva' => [
                ['path' => config('images.projects.edificio-torre-ejecutiva'), 'caption' => 'Instalación en torre corporativa'],
                ['path' => $u('photo-1563986768609-322da13575f3'), 'caption' => 'Sala de monitoreo'],
                ['path' => $u('photo-1581091226033-d5c48150dbaa'), 'caption' => 'Cámaras IP 4K'],
            ],
            'hotel-boutique' => [
                ['path' => config('images.projects.hotel-boutique'), 'caption' => 'Sistema IPTV en habitaciones'],
                ['path' => $u('photo-1582719478250-c89cae4dc85b'), 'caption' => 'Habitación con señal HD'],
                ['path' => $u('photo-1566073771259-6a8506099945'), 'caption' => 'Área común del hotel'],
            ],
            'conjunto-altos-envigado' => [
                ['path' => config('images.projects.conjunto-altos-envigado'), 'caption' => 'Paneles en zonas comunes'],
                ['path' => $u('photo-1508514177221-188b1cf16e7d'), 'caption' => 'Inversor y tablero solar'],
            ],
            'centro-comercial-mayorca' => [
                ['path' => config('images.projects.centro-comercial-mayorca'), 'caption' => 'Control de acceso biométrico'],
                ['path' => $u('photo-1573164713714-d95e436ab8d6'), 'caption' => 'Torniquetes y lectores'],
            ],
            'textiles-medellin' => [
                ['path' => config('images.projects.textiles-medellin'), 'caption' => 'Rack y cableado estructurado'],
                ['path' => $u('photo-1558494949-ef010cbdcc31'), 'caption' => 'Fibra óptica y switches'],
            ],
            'apartamento-laureles' => [
                ['path' => config('images.projects.apartamento-laureles'), 'caption' => 'Automatización del hogar'],
                ['path' => $u('photo-1558002038-1055907df827'), 'caption' => 'Control de iluminación y clima'],
            ],
        ];

        foreach ($projects as $p) {
            $serviceSlug = $p['service_slug'] ?? null;
            unset($p['service_slug']);

            if ($serviceSlug) {
                $p['service_id'] = Service::query()->where('slug', $serviceSlug)->value('id');
            }

            $project = Project::create($p);

            foreach ($galleries[$p['slug']] ?? [['path' => $p['image']]] as $order => $img) {
                ProjectImage::create([
                    'project_id'  => $project->id,
                    'path'        => $img['path'],
                    'caption'     => $img['caption'] ?? null,
                    'sort_order'  => $order,
                ]);
            }
        }
    }
}
