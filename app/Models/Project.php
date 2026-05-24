<?php

namespace App\Models;

use App\Support\ResolvesMediaPath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use ResolvesMediaPath;

    protected $fillable = [
        'title', 'slug', 'category', 'description',
        'location', 'image', 'is_featured', 'year',
    ];

    protected $casts = ['is_featured' => 'boolean'];

    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function getImageUrlAttribute(): string
    {
        $cover = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->orderBy('sort_order')->first();

        if ($cover) {
            return $cover->url;
        }

        $url = $this->resolveMediaUrl($this->image);

        $url ??= config("images.projects.{$this->slug}");

        if (is_string($url) && ! str_starts_with($url, 'http') && ! str_starts_with($url, '/')) {
            $url = asset($url);
        }

        return $url ?? asset('images/projects/placeholder.svg');
    }

    public function getCategoryColorAttribute(): string
    {
        return match(true) {
            str_contains($this->category, 'Seguridad') => '#1e3a5f',
            str_contains($this->category, 'Solar')      => '#1a3a1e',
            str_contains($this->category, 'IPTV')       => '#3a1e1e',
            str_contains($this->category, 'Domótica')   => '#2d1e3a',
            str_contains($this->category, 'Acceso')     => '#1e3a38',
            str_contains($this->category, 'Redes')      => '#334155',
            default                                     => '#178f82',
        };
    }
}
