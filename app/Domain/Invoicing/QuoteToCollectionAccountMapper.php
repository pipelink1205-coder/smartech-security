<?php

namespace App\Domain\Invoicing;

use App\Models\CollectionAccount;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;

/**
 * Copia cliente, ítems y totales de una cotización a una cuenta de cobro (no DIAN).
 */
class QuoteToCollectionAccountMapper
{
    public function fromQuote(Quote $quote): CollectionAccount
    {
        $quote->loadMissing('items');
        $quote->recalculateTotals();
        $quote->refresh();

        if ($quote->items->isEmpty()) {
            throw new \InvalidArgumentException(
                'La cotización no tiene ítems. Agrega conceptos antes de generar la cuenta de cobro.'
            );
        }

        return DB::transaction(function () use ($quote) {
            $bank = config('quotes.collection_bank', []);
            $quote->loadMissing('client');
            $client = $quote->client;

            $account = CollectionAccount::create([
                'number' => CollectionAccount::nextNumber(),
                'quote_id' => $quote->id,
                'status' => 'issued',
                'client_name' => $quote->company ?: $quote->name,
                'client_document' => $client?->document,
                'client_email' => $quote->email,
                'client_phone' => $quote->phone,
                'client_address' => $quote->client_address ?: $client?->address,
                'concept' => $quote->project_title ?: $quote->service,
                'subtotal' => round((float) $quote->subtotal, 2),
                'discount_total' => round((float) $quote->discount_total, 2),
                'tax_total' => round((float) $quote->tax_total, 2),
                'total' => round((float) $quote->grand_total, 2),
                'bank_name' => $bank['name'] ?? null,
                'bank_account_type' => $bank['account_type'] ?? null,
                'bank_account_number' => $bank['account_number'] ?? null,
                'bank_account_holder' => $bank['account_holder'] ?? null,
                'bank_nit' => $bank['nit'] ?? null,
                'payment_instructions' => $bank['instructions'] ?? config('quotes.default_payment_terms'),
                'issued_at' => now(),
                'user_id' => auth()->id(),
            ]);

            foreach ($quote->items as $index => $item) {
                /** @var QuoteItem $item */
                $qty = max(0.01, (float) $item->quantity);
                $lineTotal = round((float) $item->line_total, 2);
                $unitPrice = round($lineTotal / $qty, 2);
                $description = trim((string) ($item->concept ?: $item->description ?: 'Servicio'));

                $account->items()->create([
                    'description' => $description,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'quote_item_id' => $item->id,
                    'sort_order' => $index,
                ]);
            }

            return $account->fresh(['items', 'quote']);
        });
    }
}
