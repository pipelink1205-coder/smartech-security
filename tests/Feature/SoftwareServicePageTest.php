<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftwareServicePageTest extends TestCase
{
    use RefreshDatabase;

    private function createSoftwareService(): Service
    {
        return Service::query()->create([
            'name' => 'Desarrollo de Software e Inteligencia de Datos',
            'slug' => 'desarrollo-software',
            'description' => 'Aplicaciones web, software empresarial, automatización e inteligencia de datos para optimizar los procesos de tu organización.',
            'icon' => 'code',
            'image' => config('images.services.desarrollo-software'),
            'is_active' => true,
            'show_on_home' => true,
            'order' => 14,
            'faqs' => [
                ['question' => '¿Desarrollan software completamente a la medida?', 'answer' => 'Sí, según los requerimientos del proyecto.'],
            ],
        ]);
    }

    public function test_specialized_page_renders_content_and_search_metadata(): void
    {
        $this->createSoftwareService();

        $this->get('/servicios/desarrollo-software')
            ->assertOk()
            ->assertSee('Software que se adapta a tu empresa, no tu empresa al software.')
            ->assertSee('¿Tu empresa todavía trabaja así?')
            ->assertSee('Convierte tus datos en decisiones')
            ->assertSee('Inteligencia Artificial aplicada a procesos reales')
            ->assertSee('Un ecosistema tecnológico para tu empresa')
            ->assertSee('Soluciones que podemos construir')
            ->assertSee('¿Desarrollan software completamente a la medida?')
            ->assertSee('Desarrollo de Software a la Medida | Smart Tech Security')
            ->assertSee('name="description" content="Desarrollo de software a la medida', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('Hablar con un especialista')
            ->assertSee('/ir/whatsapp?from=service_hero', false);
    }

    public function test_service_appears_on_home_catalog_contact_form_and_dynamic_sitemap(): void
    {
        $service = $this->createSoftwareService();
        $serviceUrl = route('servicios.show', $service->slug);

        $this->get('/')
            ->assertOk()
            ->assertSee($service->name)
            ->assertSee('Conocer soluciones')
            ->assertSee($serviceUrl, false)
            ->assertSee('images/services/desarrollo-software.svg', false);

        $this->get('/servicios')
            ->assertOk()
            ->assertSee($service->name)
            ->assertSee($serviceUrl, false);

        $this->get(route('contacto', ['servicio' => $service->name]))
            ->assertOk()
            ->assertSee('<option value="'.$service->name.'" selected', false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('https://smarttechsecurity.com.co/servicios/desarrollo-software');
    }
}
