<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'source',
        'page_url',
        'page_title',
        'ip',
        'user_agent',
        'status',
        'click_count',
    ];

    protected $casts = [
        'click_count' => 'integer',
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

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }
}
