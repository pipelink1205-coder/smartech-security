<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Servicios base del sitio. Es idempotente: usa updateOrCreate por 'slug',
     * así que se puede ejecutar varias veces (incluso en producción) sin duplicar.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Cámaras de Seguridad 4K',
                'slug' => 'camaras-4k',
                'icon' => '📹',
                'order' => 1,
                'price_from' => 800000,
                'highlight' => 'Instalación en 24 horas',
                'image' => config('images.services.camaras-4k'),
                'description' => 'Videovigilancia IP Ultra HD para hogares y empresas en Medellín. Visión nocturna full color, detección por IA y acceso remoto desde tu celular.',
                'features' => ['Cámaras IP 4K Ultra HD', 'Visión nocturna Starlight', 'Reconocimiento facial y placas', 'Alertas inteligentes al móvil'],
            ],
            [
                'name' => 'Energía Solar Fotovoltaica',
                'slug' => 'energia-solar',
                'icon' => '☀️',
                'order' => 2,
                'price_from' => 4000000,
                'highlight' => 'Ahorro desde el primer mes',
                'image' => config('images.services.energia-solar'),
                'description' => 'Reduce tu factura hasta 90% con sistemas conectados a la red o híbridos con baterías. Ideales para hogares, edificios y empresas.',
                'features' => ['Paneles de alta eficiencia', 'Inversores y baterías de litio', 'Estudio de consumo gratuito', 'Financiación hasta 12 meses'],
            ],
            [
                'name' => 'Control de Acceso Biométrico',
                'slug' => 'control-acceso',
                'icon' => '🔐',
                'order' => 3,
                'price_from' => 1500000,
                'highlight' => 'Sin contacto físico',
                'image' => config('images.services.control-acceso'),
                'description' => 'Lectores de huella, reconocimiento facial 3D, tarjetas de proximidad y cerraduras inteligentes para empresas y conjuntos.',
                'features' => ['Reconocimiento facial avanzado', 'Control horario y asistencia', 'Reportes PDF/Excel', 'Integración con nómina'],
            ],
            [
                'name' => 'Alarmas de Seguridad',
                'slug' => 'alarmas',
                'icon' => '🚨',
                'order' => 4,
                'price_from' => 900000,
                'highlight' => 'Monitoreo 24/7',
                'image' => config('images.services.alarmas'),
                'description' => 'Sistemas contra intrusión con sensores PIR, barreras perimetrales, contactos magnéticos y monitoreo con central propia.',
                'features' => ['Sensores inalámbricos y cableados', 'Notificaciones push instantáneas', 'Activación por zonas', 'Botón de pánico silencioso'],
            ],
            [
                'name' => 'Domótica y Casas Inteligentes',
                'slug' => 'domotica',
                'icon' => '🏠',
                'order' => 5,
                'price_from' => 3000000,
                'highlight' => 'Vive tu casa inteligente',
                'image' => config('images.services.domotica'),
                'description' => 'Automatización de iluminación, cortinas, clima y riego. Control por voz con Alexa y Google Home desde cualquier lugar.',
                'features' => ['Control por voz y app móvil', 'Escenas personalizables', 'Ahorro energético 30-40%', 'Compatible con asistentes'],
            ],
            [
                'name' => 'Fibra Óptica y Redes',
                'slug' => 'redes-fibra',
                'icon' => '📡',
                'order' => 6,
                'price_from' => 1200000,
                'highlight' => 'Garantía de 5 años',
                'image' => config('images.services.redes-fibra'),
                'description' => 'Cableado Cat6/6A, fibra óptica, WiFi empresarial mesh y certificación de puntos de red con equipo Fluke.',
                'features' => ['Certificación Fluke oficial', 'Redes mesh empresariales', 'Cableado organizado y etiquetado', 'Soporte remoto 24/7'],
            ],
            [
                'name' => 'IPTV para Hoteles',
                'slug' => 'iptv-hoteles',
                'icon' => '📺',
                'order' => 7,
                'price_from' => null,
                'highlight' => 'Especialistas en hoteles',
                'image' => config('images.iptv.primary'),
                'description' => 'Televisión por internet HD para hoteles, hostales y apartahoteles en Medellín y el Valle de Aburrá: +200 canales, Video On Demand, integración con tu PMS y WiFi de alta velocidad en cada habitación.',
                'features' => ['+200 canales HD nacionales e internacionales', 'Video On Demand para huéspedes', 'Integración con PMS y facturación hotelera', 'WiFi de alta velocidad por habitación'],
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
