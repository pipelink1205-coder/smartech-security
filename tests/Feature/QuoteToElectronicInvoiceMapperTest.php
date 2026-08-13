<?php

namespace Tests\Feature;

use App\Domain\Invoicing\QuoteToElectronicInvoiceMapper;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteToElectronicInvoiceMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_maps_quote_client_items_and_totals(): void
    {
        $quote = Quote::create([
            'name' => 'Ana Pérez',
            'phone' => '3001112233',
            'email' => 'ana@example.com',
            'company' => 'Ana SAS',
            'client_address' => 'Calle 1 # 2-3',
            'service' => 'Cámaras',
            'status' => 'accepted',
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'type' => 'product',
            'concept' => 'Cámara IP',
            'description' => 'Cámara 4K',
            'quantity' => 2,
            'unit' => 'unidad',
            'unit_price' => 100000,
            'tax_rate' => 19,
        ]);

        $quote->recalculateTotals();

        $invoice = app(QuoteToElectronicInvoiceMapper::class)->fromQuote($quote->fresh('items'), assignNumber: false);

        $this->assertSame($quote->id, $invoice->quote_id);
        $this->assertSame('Ana SAS', $invoice->client_name);
        $this->assertSame('ana@example.com', $invoice->client_email);
        $this->assertSame('3001112233', $invoice->client_phone);
        $this->assertSame('PENDING', $invoice->dian_status);
        $this->assertSame(1, $invoice->details()->count());

        $line = $invoice->details()->first();
        $this->assertSame('Cámara IP', $line->description);
        $this->assertSame(2.0, (float) $line->quantity);
        $this->assertEqualsWithDelta(119000.0, (float) $line->price, 0.01);
        $this->assertEqualsWithDelta((float) $quote->grand_total, (float) $invoice->total, 0.01);
    }

    public function test_rejects_quote_without_items(): void
    {
        $quote = Quote::create([
            'name' => 'Sin ítems',
            'phone' => '3000000000',
            'email' => 'x@example.com',
            'service' => 'Cámaras',
            'status' => 'draft',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        app(QuoteToElectronicInvoiceMapper::class)->fromQuote($quote);
    }
}
