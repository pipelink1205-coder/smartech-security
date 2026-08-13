<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\URL;

/**
 * Factura electrónica DIAN (independiente del POS/restaurante).
 * Opcionalmente vinculada a una cotización (quote_id) para el flujo Smart Tech.
 */
class ElectronicInvoice extends Model
{
    protected $table = 'electronic_invoices';

    protected $fillable = [
        'quote_id',
        'document_type',
        'dian_prefijo',
        'dian_numero',
        'dian_resolution_id',
        'subtotal',
        'iva',
        'ico',
        'descuento_total',
        'total',
        'total_a_pagar',
        'payment_method',
        'client_name',
        'client_document',
        'client_tipo_documento',
        'client_dv',
        'client_email',
        'client_phone',
        'client_address',
        'client_city_code',
        'client_dept_code',
        'dian_status',
        'cufe',
        'dian_zip_id',
        'dian_response_code',
        'dian_description',
        'dian_errors',
        'xml_path',
        'ar_path',
        'pdf_path',
        'qr_url',
        'sent_at',
        'accepted_at',
        'user_id',
    ];

    protected $casts = [
        'dian_numero'     => 'integer',
        'subtotal'        => 'decimal:2',
        'iva'             => 'decimal:2',
        'ico'             => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'total'           => 'decimal:2',
        'total_a_pagar'   => 'decimal:2',
        'sent_at'         => 'datetime',
        'accepted_at'     => 'datetime',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ElectronicInvoiceItem::class)->orderBy('id');
    }

    public function items(): HasMany
    {
        return $this->details();
    }

    public function resolution(): BelongsTo
    {
        return $this->belongsTo(DianResolution::class, 'dian_resolution_id');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(DianCreditNote::class);
    }

    public function events(): MorphMany
    {
        return $this->morphMany(DianEvent::class, 'documentable');
    }

    public function getFullNumberAttribute(): ?string
    {
        if (!$this->dian_prefijo && !$this->dian_numero) {
            return null;
        }

        return trim(($this->dian_prefijo ?? '').($this->dian_numero ?? ''));
    }

    public function isElectronic(): bool
    {
        return ($this->document_type ?? '01') === '01'
            && ! empty($this->dian_prefijo)
            && ! empty($this->dian_numero);
    }

    public function getDisplayNumberAttribute(): string
    {
        return $this->full_number ?: ('BORRADOR-'.$this->id);
    }

    /**
     * Link wa.me con mensaje prellenado (correo/teléfono del envío pueden sobrescribirse).
     */
    public function whatsappLink(?string $phone = null, ?string $pdfUrl = null): string
    {
        $digits = preg_replace('/\D+/', '', (string) ($phone ?: $this->client_phone));
        if (strlen($digits) === 10) {
            $digits = '57'.$digits;
        }

        $number = $this->display_number;
        $total = number_format((float) ($this->total_a_pagar ?: $this->total), 0, ',', '.');
        $msg = "Hola {$this->client_name}, le compartimos la factura {$number} de Smart Tech Security por \${$total} COP.";
        if ($pdfUrl) {
            $msg .= " Descargar: {$pdfUrl}";
        }

        return 'https://wa.me/'.$digits.'?text='.urlencode($msg);
    }

    public function temporaryPdfUrl(): string
    {
        return URL::temporarySignedRoute(
            'invoices.pdf',
            now()->addDays(7),
            ['invoice' => $this],
        );
    }
}
