<?php

namespace Tests\Feature;

use App\Filament\Resources\Quotes\Schemas\QuoteForm;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteServiceOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_includes_active_services_and_other(): void
    {
        Service::query()->create([
            'name' => 'Cámaras de Seguridad 4K',
            'slug' => 'camaras-4k',
            'description' => 'Prueba',
            'icon' => '📹',
            'is_active' => true,
            'order' => 1,
        ]);

        $options = QuoteForm::serviceOptions();

        $this->assertArrayHasKey('Cámaras de Seguridad 4K', $options);
        $this->assertArrayHasKey('Varios servicios', $options);
        $this->assertArrayHasKey(QuoteForm::OTHER_SERVICE, $options);
        $this->assertTrue(QuoteForm::isCatalogService('Cámaras de Seguridad 4K'));
        $this->assertFalse(QuoteForm::isCatalogService('Cableado para un evento'));
        $this->assertFalse(QuoteForm::isCatalogService(QuoteForm::OTHER_SERVICE));
    }
}
