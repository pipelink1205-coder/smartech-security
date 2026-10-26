<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomeCover extends Model
{
    public const CACHE_KEY = 'home_cover.current';

    public const KIND_INSTITUCIONAL = 'institucional';
    public const KIND_CAMPANA = 'campana';
    public const KIND_PROMOCION = 'promocion';
    public const KIND_TEMPORADA = 'temporada';
    public const KIND_AIRE = 'aire';

    public const KINDS = [
        self::KIND_INSTITUCIONAL => 'Institucional',
        self::KIND_CAMPANA => 'Campaña',
        self::KIND_PROMOCION => 'Promoción',
        self::KIND_TEMPORADA => 'Temporada',
        self::KIND_AIRE => 'Cambio de aire',
    ];

    protected $fillable = [
        'name', 'slug', 'kind', 'badge', 'title', 'subtitle',
        'image', 'image_alt',
        'cta_primary_label', 'cta_primary_url',
        'cta_secondary_label', 'cta_secondary_url',
        'strip_label', 'strip_highlights', 'stats',
        'is_published', 'is_fallback', 'priority',
        'starts_at', 'ends_at',
    ];

    protected $casts = [
        'strip_highlights' => 'array',
        'stats' => 'array',
        'is_published' => 'boolean',
        'is_fallback' => 'boolean',
        'priority' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        $forget = static fn () => Cache::forget(self::CACHE_KEY);

        static::saved(function (self $cover) use ($forget): void {
            if ($cover->is_fallback) {
                static::query()
                    ->whereKeyNot($cover->id)
                    ->where('is_fallback', true)
                    ->update(['is_fallback' => false]);
            }

            $forget();
        });

        static::deleting(function (self $cover): bool {
            return ! $cover->is_fallback;
        });

        static::deleted($forget);
    }

    /**
     * Portada que ve el visitante: campaña vigente, o el respaldo institucional,
     * o la copia de config/site.php si aún no hay filas.
     */
    public static function current(): self
    {
        try {
            if ($preview = static::previewFromRequest()) {
                return $preview;
            }

            return Cache::remember(self::CACHE_KEY, 120, function () {
                return static::resolvePublished() ?? static::fromSiteConfig();
            });
        } catch (\Throwable) {
            return static::fromSiteConfig();
        }
    }

    public static function resolvePublished(): ?self
    {
        $now = now();

        $scheduled = static::query()
            ->where('is_published', true)
            ->where('is_fallback', false)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->orderByDesc('priority')
            ->orderByDesc('starts_at')
            ->first();

        if ($scheduled) {
            return $scheduled;
        }

        return static::query()
            ->where('is_published', true)
            ->where('is_fallback', true)
            ->first();
    }

    public static function fromSiteConfig(): self
    {
        $heroImage = config('images.hero');

        return new static([
            'name' => 'Portada institucional',
            'slug' => 'institucional',
            'kind' => self::KIND_INSTITUCIONAL,
            'badge' => config('site.hero.badge'),
            'title' => config('site.hero.title'),
            'subtitle' => config('site.hero.subtitle'),
            'image' => is_string($heroImage) && ! str_starts_with($heroImage, 'http') ? $heroImage : null,
            'image_alt' => 'Instalación de cámaras de seguridad y sistemas tecnológicos en Medellín',
            'cta_primary_label' => 'Ver Servicios',
            'cta_primary_url' => '#servicios',
            'cta_secondary_label' => 'Agendar visita gratuita',
            'cta_secondary_url' => '/?intent=visit#contacto',
            'strip_label' => config('site.strip_label'),
            'strip_highlights' => config('site.strip_highlights'),
            'stats' => config('site.stats'),
            'is_published' => true,
            'is_fallback' => true,
            'priority' => 0,
        ]);
    }

    public function replicateForEdition(): self
    {
        $copy = $this->replicate();
        $copy->name = $this->name.' (copia)';
        $copy->slug = $this->slug.'-copia-'.substr((string) uniqid(), -4);
        $copy->is_published = false;
        $copy->is_fallback = false;
        $copy->starts_at = null;
        $copy->ends_at = null;
        $copy->save();

        return $copy;
    }

    public function isPreview(): bool
    {
        return $this->exists && request()->integer('preview_portada') === (int) $this->id;
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind] ?? $this->kind;
    }

    public function lifecycle(): string
    {
        if (! $this->is_published) {
            return 'borrador';
        }

        if ($this->starts_at?->isFuture()) {
            return 'programada';
        }

        if ($this->ends_at?->isPast()) {
            return 'finalizada';
        }

        return $this->is_fallback ? 'respaldo' : 'en_vivo';
    }

    public function displayStats(): array
    {
        $stats = $this->asList($this->stats);

        return $stats !== [] ? $stats : (array) config('site.stats', []);
    }

    public function displayStripLabel(): string
    {
        return filled($this->strip_label) ? $this->strip_label : (string) config('site.strip_label');
    }

    /** @return list<string> */
    public function displayHighlights(): array
    {
        $highlights = $this->asList($this->strip_highlights);

        return $highlights !== []
            ? array_values($highlights)
            : array_values((array) config('site.strip_highlights', []));
    }

    /** @return list<mixed> */
    private function asList(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    public function primaryHref(): string
    {
        return $this->cta_primary_url ?: '#servicios';
    }

    public function secondaryHref(): string
    {
        return $this->cta_secondary_url ?: '/?intent=visit#contacto';
    }

    public function pageLinkFor(string $url): ?string
    {
        foreach (['servicios', 'contacto', 'inicio', 'iptv', 'proyectos', 'faq'] as $section) {
            if (str_contains($url, '#'.$section)) {
                return $section;
            }
        }

        return null;
    }

    public function secondaryQuoteIntent(): ?string
    {
        return str_contains($this->secondaryHref(), 'intent=visit') ? 'visit' : null;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }

            $path = public_path($this->image);
            if (is_file($path)) {
                return asset($this->image).'?v='.(filemtime($path) ?: time());
            }
        }

        $fallback = (string) config('images.hero');
        if (str_starts_with($fallback, 'http')) {
            return $fallback;
        }

        $path = public_path($fallback);

        return asset($fallback).'?v='.(@filemtime($path) ?: time());
    }

    protected static function previewFromRequest(): ?self
    {
        $id = request()->integer('preview_portada');
        if ($id < 1 || ! auth()->check()) {
            return null;
        }

        return static::query()->find($id);
    }
}
