<?php

namespace Tests\Feature;

use App\Domain\Invoicing\QuoteToCollectionAccountMapper;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteToCollectionAccountMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_maps_quote_into_collection_account(): void
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

        $account = app(QuoteToCollectionAccountMapper::class)->fromQuote($quote->fresh('items'));

        $this->assertSame($quote->id, $account->quote_id);
        $this->assertSame('Ana SAS', $account->client_name);
        $this->assertSame('ana@example.com', $account->client_email);
        $this->assertMatchesRegularExpression('/^CC-\d{4}-\d{4}$/', $account->number);
        $this->assertSame(1, $account->items()->count());
        $this->assertSame('Cámara IP', $account->items()->first()->description);
        $this->assertEqualsWithDelta((float) $quote->grand_total, (float) $account->total, 0.01);
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

        app(QuoteToCollectionAccountMapper::class)->fromQuote($quote);
    }
}
