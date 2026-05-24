<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Servicios ──────────────────────────────────────────
        $services = [
            ['name' => 'Cámaras de Seguridad 4K',  'slug' => 'camaras-4k',   'icon' => '📹', 'order' => 1, 'price_from' => 800000,
             'description' => 'Instalación profesional de CCTV con grabación en la nube, visión nocturna y acceso remoto.'],
            ['name' => 'Energía Solar',             'slug' => 'energia-solar', 'icon' => '☀️', 'order' => 2, 'price_from' => 4000000,
             'description' => 'Paneles solares fotovoltaicos, baterías de respaldo y sistemas híbridos. Reducción de hasta el 80%.'],
            ['name' => 'IPTV para Hoteles',         'slug' => 'iptv-hoteles',  'icon' => '📺', 'order' => 3, 'price_from' => 2000000,
             'description' => '+200 canales HD, gestión centralizada, Netflix integrado y soporte técnico dedicado.'],
            ['name' => 'Domótica & Smart Home',     'slug' => 'domotica',      'icon' => '🏠', 'order' => 4, 'price_from' => 3000000,
             'description' => 'Automatización de iluminación, clima, cerraduras y electrodomésticos desde una sola app.'],
            ['name' => 'Cerraduras Digitales',      'slug' => 'cerraduras',    'icon' => '🔐', 'order' => 5, 'price_from' => 600000,
             'description' => 'Biométricas, código PIN y llave digital. Compatible con Airbnb, hoteles y edificios.'],
            ['name' => 'Redes & Conectividad',      'slug' => 'redes',         'icon' => '📡', 'order' => 6, 'price_from' => 1200000,
             'description' => 'Redes WiFi empresarial, cableado estructurado y puntos de acceso Ubiquiti / Cisco Meraki.'],
        ];

        foreach ($services as $s) Service::create($s);

        // ── Proyectos ──────────────────────────────────────────
        $projects = [
            ['title' => 'Edificio Empresarial El Poblado', 'category' => 'Seguridad',  'location' => 'El Poblado, Medellín', 'year' => 2024, 'is_featured' => true,
             'description' => 'Sistema CCTV 4K + control de acceso biométrico para 8 pisos.'],
            ['title' => 'Finca Laureles – Off-Grid',       'category' => 'Solar',       'location' => 'Laureles, Medellín',   'year' => 2024, 'is_featured' => true,
             'description' => '20kW de paneles solares con batería LiFePO4 de respaldo.'],
            ['title' => 'Hotel Boutique Centro Histórico', 'category' => 'IPTV',        'location' => 'Centro, Medellín',     'year' => 2023, 'is_featured' => true,
             'description' => '60 habitaciones con sistema IPTV HD y Netflix integrado.'],
            ['title' => 'Casa Inteligente Los Balsos',     'category' => 'Domótica',    'location' => 'El Poblado, Medellín', 'year' => 2024, 'is_featured' => true,
             'description' => 'Control total por voz y app: luces, clima, cerraduras y cámaras.'],
            ['title' => 'Apartahotel Provenza',            'category' => 'Cerraduras',  'location' => 'El Poblado, Medellín', 'year' => 2023, 'is_featured' => true,
             'description' => '30 cerraduras digitales conectadas con panel de gestión centralizado.'],
            ['title' => 'Centro Comercial Envigado',       'category' => 'Redes',       'location' => 'Envigado',             'year' => 2024, 'is_featured' => true,
             'description' => 'Red WiFi empresarial Ubiquiti para 3.000 m² de área comercial.'],
        ];

        foreach ($projects as $p) Project::create($p);
    }
}
