<?php

namespace Tests\Feature;

use App\Livewire\PhDiagnosticForm;
use App\Livewire\ServiceLeadForm;
use App\Models\Quote;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Nicho Propiedad Horizontal (/unidades-residenciales) y formularios contextuales.
 */
class PropiedadHorizontalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_hub_service_local_and_diagnostic_pages_render(): void
    {
        $this->get('/unidades-residenciales')->assertOk()->assertSee('unidades residenciales')->assertSee('data-m-cta', false);
        $this->get('/unidades-residenciales/diagnostico-gratuito')->assertOk()->assertSee('Diagnóstico de seguridad');
        $this->get('/unidades-residenciales/camaras-de-seguridad')->assertOk()->assertSee('Cámaras de seguridad para unidades residenciales');
        $this->get('/unidades-residenciales/control-de-acceso/medellin')
            ->assertOk()
            ->assertSee('Control de acceso para unidades residenciales en')
            ->assertSee('BreadcrumbList', false)
            ->assertSee('FAQPage', false);
    }

    public function test_unknown_service_or_municipality_returns_404(): void
    {
        $this->get('/unidades-residenciales/drones')->assertNotFound();
        $this->get('/unidades-residenciales/camaras-de-seguridad/narnia')->assertNotFound();
    }

    public function test_old_urls_redirect_permanently(): void
    {
        $this->get('/conjuntos-residenciales/citofonia-ip/envigado')
            ->assertStatus(301)
            ->assertRedirect('/unidades-residenciales/citofonia-ip/envigado');
        $this->get('/diagnostico-gratuito-conjuntos')
            ->assertStatus(301)
            ->assertRedirect('/unidades-residenciales/diagnostico-gratuito');
        $this->get('/unidades-residenciales/mantenimiento/medellin-el-poblado')
            ->assertStatus(301)
            ->assertRedirect('/unidades-residenciales/mantenimiento/medellin');
    }

    public function test_sitemap_lists_all_ph_pages(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        // hub + diagnóstico + 4 servicios + 4×6 municipios
        $this->assertSame(30, substr_count($xml, '/unidades-residenciales'));
        $this->assertStringNotContainsString('conjuntos-residenciales', $xml);
    }

    public function test_ph_diagnostic_form_requires_consent_and_saves_lead(): void
    {
        Mail::fake();

        Livewire::test(PhDiagnosticForm::class, ['preServicio' => 'camaras-de-seguridad', 'preMunicipio' => 'envigado'])
            ->assertSet('municipio', 'envigado')
            ->assertSet('needs', ['camaras-de-seguridad'])
            ->set('name', 'Prueba Administradora')
            ->set('role', 'Administrador(a)')
            ->set('conjunto', 'Unidad Prueba')
            ->set('phone', '300 000 0000')
            ->call('submit')
            ->assertHasErrors(['consent' => 'accepted'])
            ->set('consent', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $quote = Quote::query()->latest('id')->first();
        $this->assertSame(PhDiagnosticForm::SERVICE_NAME, $quote->service);
        $this->assertSame('Unidad Prueba', $quote->company);
        $this->assertSame('Envigado', $quote->zone);
        $this->assertStringContainsString('Ley 1581', $quote->message);
    }

    public function test_service_lead_form_uses_service_specific_questions(): void
    {
        Mail::fake();
        Service::query()->create([
            'name' => 'IPTV para Hoteles', 'slug' => 'iptv-hoteles', 'description' => 'IPTV',
            'icon' => 'tv', 'is_active' => true, 'order' => 1,
        ]);

        $this->get('/servicios/iptv-hoteles')
            ->assertOk()
            ->assertSee('¿Cuántas habitaciones tiene el hotel?')
            ->assertSee('id="contacto-servicio"', false);

        Livewire::test(ServiceLeadForm::class, ['serviceSlug' => 'iptv-hoteles'])
            ->assertSet('serviceName', 'IPTV para Hoteles')
            ->set('answers.habitaciones', '20 a 50')
            ->set('name', 'Hotel Prueba')
            ->set('phone', '3000000000')
            ->set('consent', true)
            ->call('submit')
            ->assertHasNoErrors();

        $quote = Quote::query()->latest('id')->first();
        $this->assertSame('IPTV para Hoteles', $quote->service);
        $this->assertStringContainsString('¿Cuántas habitaciones tiene el hotel? 20 a 50', $quote->message);
    }
}
