<?php

namespace App\Models;

use App\Support\SpanishPesos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;

class CollectionAccount extends Model
{
    public const STATUSES = [
        'draft' => 'Borrador',
        'issued' => 'Emitida',
        'sent' => 'Enviada',
        'paid' => 'Pagada',
        'cancelled' => 'Anulada',
    ];

    protected $fillable = [
        'number',
        'quote_id',
        'status',
        'client_name',
        'client_document',
        'client_email',
        'client_phone',
        'client_address',
        'concept',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'bank_name',
        'bank_account_type',
        'bank_account_number',
        'bank_account_holder',
        'bank_nit',
        'payment_instructions',
        'notes',
        'pdf_path',
        'issued_at',
        'sent_at',
        'paid_at',
        'user_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CollectionAccountItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public static function nextNumber(): string
    {
        $year = now()->format('Y');
        $start = max(1, (int) config('quotes.collection_number_start', 1));

        $latestSeq = static::query()
            ->where('number', 'like', "CC-{$year}-%")
            ->pluck('number')
            ->map(function (?string $number): int {
                if ($number && preg_match('/CC-\d{4}-(\d+)/', $number, $m)) {
                    return (int) $m[1];
                }

                return 0;
            })
            ->max() ?: 0;

        $seq = max($latestSeq + 1, $start);

        return sprintf('CC-%s-%04d', $year, $seq);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getAmountInWordsAttribute(): string
    {
        return SpanishPesos::fromAmount((float) $this->total);
    }

    public function getPlaceAndDateAttribute(): string
    {
        $date = $this->issued_at ?? $this->created_at ?? now();
        $city = trim(explode(',', (string) config('quotes.company.city', 'Envigado'))[0]);
        $months = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];

        $month = $months[(int) $date->format('n')] ?? $date->format('F');

        return sprintf(
            '%s, %s %d del %d',
            $city !== '' ? $city : 'Envigado',
            ucfirst($month),
            (int) $date->format('j'),
            (int) $date->format('Y'),
        );
    }

    public function whatsappLink(?string $phone = null, ?string $pdfUrl = null): string
    {
        $digits = preg_replace('/\D+/', '', (string) ($phone ?: $this->client_phone));
        if (strlen((string) $digits) === 10) {
            $digits = '57'.$digits;
        }

        $total = number_format((float) $this->total, 0, ',', '.');
        $msg = "Hola {$this->client_name}, le compartimos la cuenta de cobro {$this->number} de Smart Tech Security por \${$total} COP.";
        if ($pdfUrl) {
            $msg .= " Descargar: {$pdfUrl}";
        }

        return 'https://wa.me/'.$digits.'?text='.urlencode($msg);
    }

    public function temporaryPdfUrl(): string
    {
        return URL::temporarySignedRoute(
            'collection-accounts.pdf',
            now()->addDays(7),
            ['account' => $this],
        );
    }
}
