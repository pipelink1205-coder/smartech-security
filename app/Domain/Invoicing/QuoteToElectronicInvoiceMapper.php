<?php

namespace App\Domain\Invoicing;

use App\Models\DianResolution;
use App\Models\ElectronicInvoice;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;

/**
 * Copia cliente, ítems y totales de una cotización a una factura electrónica borrador.
 * No envía a DIAN: eso lo hace DianService tras completar datos fiscales / resolución.
 */
class QuoteToElectronicInvoiceMapper
{
    public function fromQuote(Quote $quote, bool $assignNumber = true): ElectronicInvoice
    {
        $quote->loadMissing('items');
        $quote->recalculateTotals();
        $quote->refresh();

        if ($quote->items->isEmpty()) {
            throw new \InvalidArgumentException(
                'La cotización no tiene ítems. Agrega conceptos antes de generar la factura.'
            );
        }

        return DB::transaction(function () use ($quote, $assignNumber) {
            $netSubtotal = (float) $quote->items->sum(fn (QuoteItem $i) => (float) $i->net_subtotal);
            $quote->loadMissing('client');
            $client = $quote->client;

            $invoice = ElectronicInvoice::create([
                'quote_id' => $quote->id,
                'document_type' => '01',
                'subtotal' => round($netSubtotal, 2),
                'iva' => round((float) $quote->tax_total, 2),
                'ico' => 0,
                'descuento_total' => round((float) $quote->discount_total, 2),
                'total' => round((float) $quote->grand_total, 2),
                'total_a_pagar' => round((float) $quote->grand_total, 2),
                'payment_method' => 'transfer',
                'client_name' => $quote->company ?: $quote->name,
                'client_document' => $client?->document,
                'client_tipo_documento' => $client?->document_type ?: '13',
                'client_dv' => $client?->dv,
                'client_email' => $quote->email,
                'client_phone' => $quote->phone,
                'client_address' => $quote->client_address ?: $client?->address,
                'client_city_code' => $client?->city_code ?: '05001',
                'client_dept_code' => $client?->dept_code ?: '05',
                'dian_status' => 'PENDING',
                'user_id' => auth()->id(),
            ]);

            foreach ($quote->items as $item) {
                $qty = max(0.01, (float) $item->quantity);
                $priceWithTax = round((float) $item->line_total / $qty, 2);
                $description = trim((string) ($item->concept ?: $item->description ?: 'Servicio'));

                $invoice->details()->create([
                    'description' => $description,
                    'quantity' => $qty,
                    'price' => $priceWithTax,
                    'quote_item_id' => $item->id,
                ]);
            }

            if ($assignNumber) {
                $this->tryAssignNumber($invoice);
            }

            return $invoice->fresh(['details', 'quote']);
        });
    }

    /**
     * Reserva consecutivo DIAN si hay resolución activa; si no, deja la factura sin número.
     */
    public function tryAssignNumber(ElectronicInvoice $invoice): ElectronicInvoice
    {
        if ($invoice->dian_numero) {
            return $invoice;
        }

        try {
            $number = DianResolution::nextNumber();
        } catch (\Throwable) {
            return $invoice;
        }

        $invoice->update([
            'dian_prefijo' => $number['prefijo'],
            'dian_numero' => $number['numero'],
            'dian_resolution_id' => $number['resolution_id'],
        ]);

        return $invoice->refresh();
    }
}
