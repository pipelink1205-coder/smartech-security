<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_creates_draft_with_new_number_and_same_items(): void
    {
        $quote = Quote::create([
            'name' => 'Cliente Demo',
            'phone' => '3001112233',
            'email' => 'demo@example.com',
            'service' => 'Cámaras',
            'status' => 'sent',
            'project_title' => 'Torre Norte',
            'notes' => 'Nota interna',
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'type' => 'product',
            'concept' => 'Cámara IP',
            'description' => 'Cámara 4K',
            'quantity' => 4,
            'unit' => 'unidad',
            'unit_price' => 250000,
            'tax_rate' => 19,
        ]);

        $quote->refresh();
        $originalNumber = $quote->quote_number;

        $copy = $quote->duplicate();

        $this->assertNotSame($quote->id, $copy->id);
        $this->assertNotSame($originalNumber, $copy->quote_number);
        $this->assertSame('draft', $copy->status);
        $this->assertNull($copy->sent_at);
        $this->assertSame('Cliente Demo', $copy->name);
        $this->assertSame('Torre Norte', $copy->project_title);
        $this->assertSame($quote->client_id, $copy->client_id);
        $this->assertSame(1, $copy->items()->count());
        $this->assertSame('Cámara IP', $copy->items()->first()->concept);
        $this->assertSame(4.0, (float) $copy->items()->first()->quantity);
    }
}
