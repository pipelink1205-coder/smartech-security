<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\QuoteCatalogItem;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteItemPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_blank_discount_and_tax_are_normalized_before_saving(): void
    {
        $quote = Quote::create([
            'name' => 'Cliente de prueba',
            'phone' => '3001234567',
            'service' => 'Servicio técnico',
            'status' => 'draft',
        ]);

        $item = QuoteItem::create([
            'quote_id' => $quote->id,
            'type' => 'service',
            'concept' => 'Diagnóstico',
            'description' => 'Diagnóstico técnico',
            'quantity' => 1,
            'unit' => 'servicio',
            'unit_price' => 60000,
            'discount_percent' => null,
            'tax_rate' => null,
        ]);

        $this->assertSame('0.00', $item->fresh()->discount_percent);
        $this->assertSame('0.00', $item->fresh()->tax_rate);
        $this->assertSame('60000.00', $item->fresh()->line_total);
    }

    public function test_manual_quote_item_is_saved_into_commercial_catalog(): void
    {
        $quote = Quote::create([
            'name' => 'Cliente de prueba',
            'phone' => '3001234567',
            'service' => 'Servicio técnico',
            'status' => 'draft',
        ]);

        $item = QuoteItem::create([
            'quote_id' => $quote->id,
            'type' => 'product',
            'concept' => 'Switch PoE 8 puertos',
            'description' => 'Switch PoE administrable',
            'quantity' => 2,
            'unit' => 'unidad',
            'unit_price' => 450000,
            'tax_rate' => 19,
        ]);

        $item->refresh();

        $this->assertNotNull($item->quote_catalog_item_id);

        $catalog = QuoteCatalogItem::find($item->quote_catalog_item_id);
        $this->assertNotNull($catalog);
        $this->assertSame('Switch PoE 8 puertos', $catalog->name);
        $this->assertSame('product', $catalog->type);
        $this->assertTrue($catalog->is_active);
        $this->assertSame('450000.00', $catalog->default_unit_price);
    }

    public function test_manual_quote_item_reuses_existing_catalog_by_name_and_type(): void
    {
        $existing = QuoteCatalogItem::create([
            'type' => 'service',
            'code' => 'DIAG_EXISTENTE',
            'name' => 'Diagnóstico avanzado',
            'description' => 'Plantilla previa',
            'unit' => 'servicio',
            'default_unit_price' => 80000,
            'default_tax_rate' => 19,
            'is_active' => true,
        ]);

        $quote = Quote::create([
            'name' => 'Cliente de prueba',
            'phone' => '3001234567',
            'service' => 'Servicio técnico',
            'status' => 'draft',
        ]);

        $item = QuoteItem::create([
            'quote_id' => $quote->id,
            'type' => 'service',
            'concept' => 'Diagnóstico avanzado',
            'description' => 'Otra descripción en la línea',
            'quantity' => 1,
            'unit' => 'servicio',
            'unit_price' => 90000,
            'tax_rate' => 19,
        ]);

        $this->assertSame($existing->id, $item->fresh()->quote_catalog_item_id);
        $this->assertSame(1, QuoteCatalogItem::query()->where('name', 'Diagnóstico avanzado')->count());
    }

    public function test_pdf_does_not_restore_commercial_sections_the_user_cleared(): void
    {
        $quote = Quote::create([
            'name' => 'Cliente de prueba',
            'phone' => '3001234567',
            'service' => 'Servicio técnico',
            'status' => 'draft',
        ]);

        $quote->forceFill([
            'terms' => null,
            'payment_terms' => null,
            'warranty_terms' => null,
        ])->save();

        $html = View::make('pdf.quote', ['quote' => $quote->fresh('items')])->render();

        $this->assertStringNotContainsString('<strong>Condiciones comerciales</strong>', $html);
        $this->assertStringNotContainsString('<strong>Forma de pago</strong>', $html);
        $this->assertStringNotContainsString('<strong>Garantía</strong>', $html);
        $this->assertStringNotContainsString('Validez: 15 días calendario.', $html);
    }
}
