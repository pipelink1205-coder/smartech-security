<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectronicInvoiceItem extends Model
{
    protected $table = 'electronic_invoice_items';

    protected $fillable = [
        'electronic_invoice_id',
        'description',
        'quantity',
        'price',
        'quote_item_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price'    => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ElectronicInvoice::class, 'electronic_invoice_id');
    }

    public function quoteItem(): BelongsTo
    {
        return $this->belongsTo(QuoteItem::class);
    }
}
