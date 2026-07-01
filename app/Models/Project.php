<?php

namespace App\Models;

use App\Support\ResolvesMediaPath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use ResolvesMediaPath;

    protected $fillable = [
        'service_id', 'title', 'slug', 'category', 'description',
        'location', 'address', 'latitude', 'longitude', 'comuna_numero', 'barrio',
        'image', 'is_featured', 'year',
    ];

    protected $casts = [
        'is_featured'     => 'boolean',
        'latitude'        => 'float',
        'longitude'       => 'float',
        'comuna_numero'   => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if ($project->service_id) {
                $project->category = Service::query()
                    ->whereKey($project->service_id)
                    ->value('name') ?? $project->category;
            }

            if (blank($project->location) && filled($project->barrio)) {
                $project->location = Str::title(Str::lower($project->barrio)) . ', Medellín';
            }
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeOnMap($q)
    {
        return $q->whereNotNull('latitude')->whereNotNull('longitude');
    }

    public function toMapPayload(): array
    {
        return [
            'id'            => $this->id,
            'category'      => $this->service_name,
            'description'   => $this->description,
            'address'       => $this->address,
            'location'      => $this->location,
            'comuna_numero' => $this->comuna_numero,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
            'image_url'     => $this->image_url,
        ];
    }

    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
            ->orderByDesc('is_cover')
            ->orderBy('sort_order');
    }

    public function getServiceNameAttribute(): string
    {
        return $this->service?->name ?? $this->category;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->relationLoaded('images')) {
            $cover = $this->images->firstWhere('is_cover', true)
                ?? $this->images->sortBy('sort_order')->first();
        } else {
            $cover = $this->images()->where('is_cover', true)->first()
                ?? $this->images()->orderBy('sort_order')->first();
        }

        if ($cover) {
            return $cover->url;
        }

        $url = $this->resolveMediaUrl($this->image);

        $url ??= config("images.projects.{$this->slug}");

        if (is_string($url) && ! str_starts_with($url, 'http') && ! str_starts_with($url, '/')) {
            $url = '/' . ltrim($url, '/');
        }

        return $url ?? '/images/projects/placeholder.svg';
    }

    public function getCategoryColorAttribute(): string
    {
        $label = $this->service_name;

        return match (true) {
            str_contains($label, 'Seguridad') || str_contains($label, 'Cámaras') => '#1e3a5f',
            str_contains($label, 'Solar') => '#1a3a1e',
            str_contains($label, 'IPTV') => '#3a1e1e',
            str_contains($label, 'Domótica') => '#2d1e3a',
            str_contains($label, 'Acceso') => '#1e3a38',
            str_contains($label, 'Redes') || str_contains($label, 'Fibra') => '#334155',
            str_contains($label, 'Alarmas') => '#7f1d1d',
            default => '#178f82',
        };
    }
}
