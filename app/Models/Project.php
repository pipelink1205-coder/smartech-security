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
        'image', 'client_logo', 'show_in_clients_ticker', 'is_featured', 'year',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'show_in_clients_ticker' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'comuna_numero' => 'integer',
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
            $candidates = $this->images
                ->sortBy([
                    ['is_cover', 'desc'],
                    ['sort_order', 'asc'],
                ])
                ->values();
        } else {
            $candidates = $this->images()
                ->orderByDesc('is_cover')
                ->orderBy('sort_order')
                ->get();
        }

        foreach ($candidates as $cover) {
            $relative = ltrim(str_replace('\\', '/', (string) $cover->path), '/');
            if ($relative !== '' && is_file(public_path($relative))) {
                return $cover->url;
            }
        }

        $url = $this->resolveMediaUrl($this->image);

        if ($url) {
            $relative = ltrim(str_replace('\\', '/', parse_url($url, PHP_URL_PATH) ?: $url), '/');
            if (str_starts_with($url, 'http') || is_file(public_path($relative))) {
                return $url;
            }
        }

        $fromConfig = config("images.projects.{$this->slug}");
        if (is_string($fromConfig) && $fromConfig !== '') {
            if (str_starts_with($fromConfig, 'http://') || str_starts_with($fromConfig, 'https://')) {
                return $fromConfig;
            }
            $relative = ltrim($fromConfig, '/');
            if (is_file(public_path($relative))) {
                return '/' . $relative;
            }
        }

        return '/images/projects/placeholder.svg';
    }

    public function getClientLogoUrlAttribute(): ?string
    {
        return $this->resolveMediaUrl($this->client_logo);
    }

    public function scopeInClientsTicker($q)
    {
        return $q->where('show_in_clients_ticker', true)
            ->whereNotNull('client_logo')
            ->where('client_logo', '!=', '');
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
