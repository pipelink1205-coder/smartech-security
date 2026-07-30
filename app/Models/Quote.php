<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'name', 'phone', 'email', 'company', 'employees_range', 'current_it',
        'service', 'project_title', 'zone', 'client_address', 'message',
        'intent', 'preferred_visit_date', 'preferred_visit_slot',
        'price_min', 'price_max', 'tax_percent', 'valid_until',
        'currency', 'subtotal', 'discount_total', 'tax_total', 'grand_total',
        'status', 'notes', 'terms', 'payment_terms', 'warranty_terms',
        'advisor_name', 'advisor_title', 'issued_at', 'sent_at', 'accepted_at',
        'pdf_path',
    ];

    protected $casts = [
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'valid_until' => 'date',
        'preferred_visit_date' => 'date',
        'issued_at' => 'datetime',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public const STATUSES = [
        'new' => 'Nuevo lead',
        'contacted' => 'Contactado',
        'visit_scheduled' => 'Visita agendada',
        'draft' => 'Borrador',
        'quoted' => 'Cotizado',
        'sent' => 'Enviada',
        'accepted' => 'Aceptada',
        'rejected' => 'Rechazada',
        'expired' => 'Vencida',
        'cancelled' => 'Anulada',
        'closed' => 'Cerrado ✓',
        'lost' => 'Perdido',
    ];

    public const INTENTS = [
        'info' => 'Solo información',
        'visit' => 'Agendar visita',
    ];

    public const VISIT_SLOTS = [
        'morning' => 'Mañana (8:00 – 12:00)',
        'afternoon' => 'Tarde (13:00 – 17:00)',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quote $quote): void {
            if (blank($quote->quote_number)) {
                $quote->quote_number = self::nextQuoteNumber();
            }
            if (blank($quote->valid_until)) {
                $quote->valid_until = now()->addDays(15)->toDateString();
            }
            if (blank($quote->terms)) {
                $quote->terms = config('quotes.default_terms');
            }
            if (blank($quote->payment_terms)) {
                $quote->payment_terms = config('quotes.default_payment_terms');
            }
            if (blank($quote->warranty_terms)) {
                $quote->warranty_terms = config('quotes.default_warranty_terms');
            }
        });
    }

    public static function nextQuoteNumber(): string
    {
        $year = now()->format('Y');
        $latest = static::query()
            ->where('quote_number', 'like', "COT-{$year}-%")
            ->orderByDesc('id')
            ->value('quote_number');

        $seq = 1;
        if ($latest && preg_match('/COT-\d{4}-(\d+)/', $latest, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return sprintf('COT-%s-%04d', $year, $seq);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getIntentLabelAttribute(): string
    {
        return self::INTENTS[$this->intent] ?? ($this->intent ?: 'Solo información');
    }

    public function getPreferredVisitSlotLabelAttribute(): ?string
    {
        if (! $this->preferred_visit_slot) {
            return null;
        }

        return self::VISIT_SLOTS[$this->preferred_visit_slot] ?? $this->preferred_visit_slot;
    }

    public function getPreferredVisitSummaryAttribute(): ?string
    {
        if (! $this->preferred_visit_date && ! $this->preferred_visit_slot) {
            return null;
        }

        $parts = [];
        if ($this->preferred_visit_date) {
            $parts[] = $this->preferred_visit_date->format('d/m/Y');
        }
        if ($this->preferred_visit_slot_label) {
            $parts[] = $this->preferred_visit_slot_label;
        }

        return implode(' · ', $parts);
    }

    public function getPriceRangeAttribute(): string
    {
        if ($this->items()->exists()) {
            return '$'.number_format($this->grand_total, 0, ',', '.').' COP';
        }

        if (! $this->price_min) {
            return 'Por cotizar';
        }

        return '$'.number_format($this->price_min, 0, ',', '.')
            .' – $'.number_format($this->price_max, 0, ',', '.');
    }

    public function recalculateTotals(): void
    {
        $items = $this->items()->get();

        $this->forceFill([
            'subtotal' => $items->sum(fn (QuoteItem $item) => (float) $item->gross_subtotal),
            'discount_total' => $items->sum(fn (QuoteItem $item) => (float) $item->discount_amount),
            'tax_total' => $items->sum(fn (QuoteItem $item) => (float) $item->tax_amount),
            'grand_total' => $items->sum(fn (QuoteItem $item) => (float) $item->line_total),
        ])->saveQuietly();

        $this->unsetRelation('items');
    }

    public function getTaxAmountAttribute(): float
    {
        return (float) $this->tax_total;
    }

    public function getHasFormalItemsAttribute(): bool
    {
        return $this->items->isNotEmpty();
    }

    public function getWhatsappLinkAttribute(): string
    {
        $about = $this->service ?: 'su solicitud';
        $msg = urlencode("Hola {$this->name}, le contactamos de Smart Tech Security sobre {$about}.");

        return "https://wa.me/{$this->phone}?text={$msg}";
    }

    public function scopeNew($q)
    {
        return $q->where('status', 'new');
    }

    public function scopeActive($q)
    {
        return $q->whereNotIn('status', ['closed', 'lost']);
    }
}
