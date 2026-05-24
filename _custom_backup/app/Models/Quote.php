<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'service',
        'zone', 'message', 'price_min', 'price_max',
        'status', 'notes', 'pdf_path',
    ];

    protected $casts = [
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
    ];

    // Status disponibles
    const STATUSES = [
        'new'       => 'Nuevo',
        'contacted' => 'Contactado',
        'quoted'    => 'Cotizado',
        'closed'    => 'Cerrado ✓',
        'lost'      => 'Perdido',
    ];

    // Helpers
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPriceRangeAttribute(): string
    {
        if (!$this->price_min) return 'Por cotizar';
        return '$' . number_format($this->price_min, 0, ',', '.')
             . ' – $' . number_format($this->price_max, 0, ',', '.');
    }

    public function getWhatsappLinkAttribute(): string
    {
        $msg = urlencode("Hola {$this->name}, le contactamos de Smart Tech Security sobre su cotización de {$this->service}.");
        return "https://wa.me/{$this->phone}?text={$msg}";
    }

    // Scopes
    public function scopeNew($q)       { return $q->where('status', 'new'); }
    public function scopeActive($q)    { return $q->whereNotIn('status', ['closed', 'lost']); }
}
