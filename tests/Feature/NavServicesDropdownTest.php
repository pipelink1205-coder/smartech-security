<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menú v2: "Servicios" (mega menú por categoría) + "Soluciones" (por tipo de cliente).
 */
class NavServicesDropdownTest extends TestCase
{
    use RefreshDatabase;

    private function seedServices(): void
    {
        foreach ([
            ['Cámaras de Seguridad 4K', 'camaras-4k', 'camera'],
            ['Outsourcing de TI', 'outsourcing-ti', 'support'],
            ['Desarrollo de Software', 'desarrollo-software', 'code'],
            ['IPTV para Hoteles', 'iptv-hoteles', 'tv'],
            ['Servicio sin grupo', 'servicio-sin-grupo', 'shield'],
        ] as $i => [$name, $slug, $icon]) {
            Service::query()->create([
                'name' => $name, 'slug' => $slug, 'description' => $name, 'icon' => $icon,
                'is_active' => true, 'order' => $i + 1,
            ]);
        }
    }

    public function test_servicios_mega_menu_groups_services_by_category(): void
    {
        $this->seedServices();

        $this->get('/')
            ->assertOk()
            ->assertSee('nav-mega', false)
            ->assertSee('Seguridad electrónica')
            ->assertSee('Tecnología para empresas')
            ->assertSee('Cámaras de Seguridad 4K')
            ->assertSee(route('servicios.show', 'camaras-4k'), false)
            ->assertSee('Ver todos los servicios');
    }

    public function test_services_without_group_fall_into_mas_servicios(): void
    {
        $this->seedServices();

        $groups = Service::navGroups();

        $this->assertArrayHasKey('Más servicios', $groups);
        $this->assertSame(['servicio-sin-grupo'], $groups['Más servicios']->pluck('slug')->all());
        $this->assertNotContains('camaras-4k', $groups['Más servicios']->pluck('slug')->all());
    }

    public function test_soluciones_dropdown_links_by_client_type(): void
    {
        $this->seedServices();

        $this->get('/')
            ->assertOk()
            ->assertSee('Soluciones')
            ->assertSee('Unidades residenciales')
            ->assertSee(route('ph.hub'), false)
            ->assertSee(route('ph.diagnostico'), false)
            ->assertSee('Hoteles')
            ->assertSee(route('servicios.show', 'iptv-hoteles'), false)
            ->assertSee('Empresas')
            ->assertSee(route('servicios.show', 'outsourcing-ti'), false)
            ->assertSee(route('servicios.show', 'desarrollo-software'), false);
    }
}
