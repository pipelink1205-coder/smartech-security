<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    public const DOCUMENT_TYPES = [
        '31' => 'NIT (31)',
        '13' => 'Cédula (13)',
        '22' => 'Cédula de extranjería (22)',
        '41' => 'Pasaporte (41)',
        '42' => 'Documento extranjero (42)',
    ];

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'address',
        'zone',
        'document_type',
        'document',
        'dv',
        'city_code',
        'dept_code',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLabelAttribute(): string
    {
        $title = filled($this->company) ? (string) $this->company : (string) $this->name;

        if (filled($this->company) && filled($this->name) && $this->name !== $this->company) {
            $title = $this->company.' · '.$this->name;
        }

        if (filled($this->document)) {
            $prefix = ($this->document_type === '31') ? 'NIT ' : '';
            $title .= ' — '.$prefix.$this->document;
            if ($this->document_type === '31' && filled($this->dv)) {
                $title .= '-'.$this->dv;
            }
        }

        return $title;
    }

    /**
     * @return array<string, mixed>
     */
    public function quoteAttributes(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'company' => $this->company,
            'client_address' => $this->address,
            'zone' => $this->zone,
        ];
    }
}
