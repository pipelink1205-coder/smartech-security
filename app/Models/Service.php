<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'features', 'highlight', 'icon', 'image',
        'price_from', 'is_active', 'order', 'color',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features'  => 'array',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive($q)   { return $q->where('is_active', true); }
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
}
