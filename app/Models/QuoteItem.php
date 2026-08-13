<?php

namespace App\Models;

use App\Domain\Quotes\CatalogItemSync;
use App\Domain\Quotes\QuoteLineCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'quote_catalog_item_id',
        'code',
        'type',
        'concept',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_percent',
        'tax_rate',
        'gross_subtotal',
        'discount_amount',
        'net_subtotal',
        'tax_amount',
        'line_total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'gross_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (QuoteItem $item): void {
            $item->discount_percent = filled($item->discount_percent)
                ? (float) $item->discount_percent
                : 0;
            $item->tax_rate = filled($item->tax_rate)
                ? (float) $item->tax_rate
                : 0;

            $item->forceFill(QuoteLineCalculator::calculate(
                (float) $item->quantity,
                (float) $item->unit_price,
                (float) $item->discount_percent,
                (float) $item->tax_rate,
            ));

            // Ítem manual → queda en catálogo para próximas cotizaciones.
            if (blank($item->quote_catalog_item_id) && filled($item->concept)) {
                $catalog = CatalogItemSync::ensureForQuoteItem($item);
                if ($catalog) {
                    $item->quote_catalog_item_id = $catalog->id;
                    if (blank($item->code)) {
                        $item->code = $catalog->code;
                    }
                }
            }
        });

        static::saved(fn (QuoteItem $item) => $item->quote?->recalculateTotals());
        static::deleted(fn (QuoteItem $item) => $item->quote?->recalculateTotals());
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(QuoteCatalogItem::class, 'quote_catalog_item_id');
    }
}
