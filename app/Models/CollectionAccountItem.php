<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionAccountItem extends Model
{
    protected $fillable = [
        'collection_account_id',
        'description',
        'quantity',
        'unit_price',
        'line_total',
        'quote_item_id',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(CollectionAccount::class, 'collection_account_id');
    }

    public function quoteItem(): BelongsTo
    {
        return $this->belongsTo(QuoteItem::class);
    }
}
