<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavServicesDropdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_servicios_dropdown_lists_catalog_services_but_not_top_level_items(): void
    {
        Service::query()->create([
            'name' => 'Cámaras de Seguridad 4K',
            'slug' => 'camaras-4k',
            'description' => 'Videovigilancia IP.',
            'icon' => 'camera',
            'is_active' => true,
            'order' => 1,
        ]);
        Service::query()->create([
            'name' => 'Outsourcing de TI',
            'slug' => 'outsourcing-ti',
            'description' => 'Soporte TI.',
            'icon' => 'support',
            'is_active' => true,
            'order' => 2,
        ]);
        Service::query()->create([
            'name' => 'Desarrollo de Software',
            'slug' => 'desarrollo-software',
            'description' => 'Software a la medida.',
            'icon' => 'code',
            'is_active' => true,
            'order' => 3,
        ]);
        Service::query()->create([
            'name' => 'IPTV para Hoteles',
            'slug' => 'iptv-hoteles',
            'description' => 'Televisión IP.',
            'icon' => 'tv',
            'is_active' => true,
            'order' => 4,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('class="nav-item has-dropdown"', false)
            ->assertSee('Cámaras de Seguridad 4K')
            ->assertSee(route('servicios.show', 'camaras-4k'), false)
            ->assertSee('Ver todos los servicios');
    }

    public function test_iptv_dropdown_lists_openable_parts_of_the_service(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Presentación IPTV')
            ->assertSee('Ficha del servicio')
            ->assertSee('+200 canales HD')
            ->assertSee('Video On Demand')
            ->assertSee('Integración PMS')
            ->assertSee('WiFi por habitación')
            ->assertSee(route('servicios.show', 'iptv-hoteles'), false)
            ->assertSee(route('contacto', ['servicio' => 'IPTV para Hoteles']), false);
    }

    public function test_outsourcing_dropdown_lists_openable_parts_of_the_service(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Calcula tu ahorro')
            ->assertSee('Planes')
            ->assertSee('Mesa de ayuda')
            ->assertSee('Ciberseguridad incluida')
            ->assertSee('Diagnóstico gratuito')
            ->assertSee(route('servicios.show', 'outsourcing-ti').'#comparador', false)
            ->assertSee(route('servicios.show', 'outsourcing-ti').'#planes', false)
            ->assertSee(route('servicios.show', 'outsourcing-ti').'#diagnostico', false);
    }

    public function test_software_dropdown_lists_openable_parts_of_the_service(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Software a la medida')
            ->assertSee('Aplicaciones web')
            ->assertSee('Analítica de datos')
            ->assertSee('Inteligencia Artificial')
            ->assertSee('Solicitar diagnóstico')
            ->assertSee(route('servicios.show', 'desarrollo-software').'#sw-solutions', false)
            ->assertSee(route('servicios.show', 'desarrollo-software').'#sw-analytics', false)
            ->assertSee(route('servicios.show', 'desarrollo-software').'#sw-ai', false)
            ->assertSee(route('contacto', ['servicio' => 'Desarrollo de Software e Inteligencia de Datos']), false);
    }
}
