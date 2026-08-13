<?php

namespace Tests\Feature;

use App\Domain\Invoicing\QuoteToElectronicInvoiceMapper;
use App\Domain\Quotes\ClientSync;
use App\Models\Client;
use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_client_from_quote_and_links_it(): void
    {
        $quote = Quote::create([
            'name' => 'Ana Pérez',
            'phone' => '3001112233',
            'email' => 'ana@hotel.com',
            'company' => 'Hotel Selis',
            'client_address' => 'Calle 10 # 20-30',
            'zone' => 'El Poblado',
            'service' => 'Cámaras',
            'status' => 'draft',
        ]);

        $client = ClientSync::ensureForQuote($quote);

        $this->assertNotNull($client);
        $this->assertSame('Hotel Selis', $client->company);
        $this->assertSame('Ana Pérez', $client->name);
        $this->assertSame('31', $client->document_type);
        $this->assertSame($client->id, $quote->fresh()->client_id);
    }

    public function test_reuses_existing_client_by_email(): void
    {
        $existing = Client::create([
            'name' => 'Ana Pérez',
            'company' => 'Hotel Selis',
            'email' => 'ana@hotel.com',
            'phone' => '3000000000',
            'document_type' => '31',
            'document' => '900123456',
            'dv' => '1',
        ]);

        $quote = Quote::create([
            'name' => 'Ana Pérez',
            'phone' => '3001112233',
            'email' => 'ana@hotel.com',
            'company' => 'Hotel Selis',
            'service' => 'Cámaras',
            'status' => 'draft',
        ]);

        $client = ClientSync::ensureForQuote($quote);

        $this->assertSame($existing->id, $client->id);
        $this->assertSame(1, Client::query()->count());
        $this->assertSame($existing->id, $quote->fresh()->client_id);
    }

    public function test_quote_attributes_prefill_form_fields(): void
    {
        $client = Client::create([
            'name' => 'Luis Rozo',
            'company' => 'Empacor',
            'email' => 'compras@empacor.com',
            'phone' => '3105556677',
            'address' => 'Km 5 vía Girardota',
            'zone' => 'Girardota',
        ]);

        $this->assertSame([
            'name' => 'Luis Rozo',
            'phone' => '3105556677',
            'email' => 'compras@empacor.com',
            'company' => 'Empacor',
            'client_address' => 'Km 5 vía Girardota',
            'zone' => 'Girardota',
        ], $client->quoteAttributes());
    }

    public function test_invoice_mapper_copies_client_tax_id(): void
    {
        $client = Client::create([
            'name' => 'Ana Pérez',
            'company' => 'Ana SAS',
            'email' => 'ana@example.com',
            'phone' => '3001112233',
            'address' => 'Calle 1 # 2-3',
            'document_type' => '31',
            'document' => '900111222',
            'dv' => '7',
            'city_code' => '05001',
            'dept_code' => '05',
        ]);

        $quote = Quote::create([
            'client_id' => $client->id,
            'name' => 'Ana Pérez',
            'phone' => '3001112233',
            'email' => 'ana@example.com',
            'company' => 'Ana SAS',
            'client_address' => 'Calle 1 # 2-3',
            'service' => 'Cámaras',
            'status' => 'accepted',
        ]);

        $quote->items()->create([
            'type' => 'product',
            'concept' => 'Cámara IP',
            'description' => 'Cámara 4K',
            'quantity' => 1,
            'unit' => 'unidad',
            'unit_price' => 100000,
            'tax_rate' => 19,
        ]);

        $quote->recalculateTotals();

        $invoice = app(QuoteToElectronicInvoiceMapper::class)->fromQuote($quote->fresh('items'), assignNumber: false);

        $this->assertSame('900111222', $invoice->client_document);
        $this->assertSame('31', $invoice->client_tipo_documento);
        $this->assertSame('7', $invoice->client_dv);
        $this->assertSame('Ana SAS', $invoice->client_name);
    }
}
