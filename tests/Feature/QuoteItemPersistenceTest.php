<?php

namespace Tests\Feature;

use App\Models\Quote;
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
