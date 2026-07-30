<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'long_description', 'features', 'highlight', 'icon', 'image',
        'includes', 'process_steps', 'brands', 'standards', 'tools', 'faqs',
        'price_from', 'is_active', 'show_on_home', 'order', 'color',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'show_on_home'   => 'boolean',
        'features'       => 'array',
        'includes'       => 'array',
        'process_steps'  => 'array',
        'brands'         => 'array',
        'standards'      => 'array',
        'tools'          => 'array',
        'faqs'           => 'array',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive($q)   { return $q->where('is_active', true); }
    public function scopeOnHome($q)   { return $q->where('show_on_home', true); }
    public function scopeOrdered($q)  { return $q->orderBy('order'); }

    public function getImageUrlAttribute(): string
    {
        $url = null;

        if ($this->image) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                $url = $this->image;
            } elseif (file_exists(public_path($this->image))) {
                $url = asset($this->image);
            }
        }

        $url ??= config("images.services.{$this->slug}")
            ?? config('images.services.camaras-4k');

        if (is_string($url) && ! str_starts_with($url, 'http') && ! str_starts_with($url, '/')) {
            return asset($url);
        }

        return $url;
    }

    /**
     * Normaliza brands: separa entradas tipo "Grupo: a, b, c" en grupos con etiqueta.
     *
     * @return array<int, array{label: string|null, brands: array<int, string>}>
     */
    public function getBrandGroupsAttribute(): array
    {
        $general = [];
        $grouped = [];

        foreach ($this->brands ?? [] as $entry) {
            $entry = trim((string) $entry);
            if ($entry === '') {
                continue;
            }

            if (preg_match('/^([^:]{2,40}):\s*(.+)$/u', $entry, $m)) {
                $grouped[] = [
                    'label'  => trim($m[1]),
                    'brands' => array_values(array_filter(array_map('trim', explode(',', $m[2])))),
                ];
            } else {
                $general[] = $entry;
            }
        }

        $result = $general ? [['label' => null, 'brands' => $general]] : [];

        return array_merge($result, $grouped);
    }
}
