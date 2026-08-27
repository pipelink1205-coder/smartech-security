<?php

namespace App\Models;

use App\Domain\Quotes\ClientSync;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappLead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'service',
        'message',
        'source',
        'page_url',
        'page_title',
        'destination_phone',
        'click_count',
        'ip',
        'user_agent',
        'status',
        'notes',
        'quote_id',
        'contacted_at',
    ];

    protected $casts = [
        'click_count' => 'integer',
        'contacted_at' => 'datetime',
    ];

    public const STATUSES = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'quoted' => 'Pasó a cotización',
        'closed' => 'Cerrado',
        'discarded' => 'Descartado',
    ];

    public const SOURCES = [
        'fab' => 'Botón flotante',
        'service_hero' => 'Página de servicio (inicio)',
        'service_sidebar' => 'Página de servicio (cotizar)',
        'service_cta' => 'Página de servicio (final)',
        'contact' => 'Contacto',
        'footer' => 'Pie de página',
        'legal' => 'Página legal',
        'link' => 'Enlace del sitio',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getVisitorWhatsappUrlAttribute(): string
    {
        $digits = $this->destinationDigits();
        $about = $this->service ?: 'sus servicios';
        $msg = "Hola, soy {$this->name}. Me interesa {$about} y quiero información para contratar.";

        if (filled($this->message)) {
            $msg .= "\n\n".$this->message;
        }

        return 'https://wa.me/'.$digits.'?text='.urlencode($msg);
    }

    public function getAdminWhatsappUrlAttribute(): string
    {
        $about = $this->service ?: 'su solicitud por WhatsApp';
        $msg = urlencode("Hola {$this->name}, le contactamos de Smart Tech Security sobre {$about}.");

        return 'https://wa.me/'.$this->phone.'?text='.$msg;
    }

    public function markContacted(): void
    {
        if ($this->status === 'new') {
            $this->forceFill([
                'status' => 'contacted',
                'contacted_at' => now(),
            ])->save();
        }
    }

    public function toQuote(): Quote
    {
        if ($this->quote_id && $this->quote) {
            return $this->quote;
        }

        $quote = Quote::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'service' => $this->service ?: 'Varios servicios',
            'message' => $this->message,
            'status' => 'new',
            'notes' => trim(
                'Lead desde botón WhatsApp ('.$this->source_label
                .($this->page_url ? ', '.$this->page_url : '').').'
                ."\n".(string) $this->notes
            ),
        ]);

        ClientSync::ensureForQuote($quote);

        $this->forceFill([
            'quote_id' => $quote->id,
            'status' => 'quoted',
            'contacted_at' => $this->contacted_at ?? now(),
        ])->save();

        return $quote;
    }

    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (strlen($digits) === 10 && str_starts_with($digits, '3')) {
            return '57'.$digits;
        }

        return $digits;
    }

    public static function allowedDestinations(): array
    {
        return array_values(array_filter([
            preg_replace('/\D/', '', (string) config('contact.whatsapp')),
            preg_replace('/\D/', '', (string) config('contact.whatsapp_secondary')),
        ]));
    }

    private function destinationDigits(): string
    {
        $allowed = self::allowedDestinations();
        $current = preg_replace('/\D/', '', (string) $this->destination_phone);

        if ($current && in_array($current, $allowed, true)) {
            return $current;
        }

        return $allowed[0] ?? (string) config('contact.whatsapp');
    }
}
