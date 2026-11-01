<?php

namespace App\Support;

use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Helpers del nicho Propiedad Horizontal (unidades / conjuntos residenciales).
 * El contenido vive en config/propiedad-horizontal.php.
 */
final class PropiedadHorizontal
{
    public const BASE = '/unidades-residenciales';

    public const DIAGNOSTICO = '/unidades-residenciales/diagnostico-gratuito';

    /** @return array<string, array<string, mixed>> */
    public static function servicios(): array
    {
        return config('propiedad-horizontal.servicios', []);
    }

    /** @return array<string, array<string, mixed>> */
    public static function municipios(): array
    {
        return config('propiedad-horizontal.municipios', []);
    }

    /**
     * Todos los municipios que atendemos (con página + extra), para el formulario.
     *
     * @return array<string, string> slug => nombre
     */
    public static function cobertura(): array
    {
        return array_map(fn ($m) => $m['name'], self::municipios())
            + config('propiedad-horizontal.cobertura_extra', []);
    }

    public static function servicio(string $slug): ?array
    {
        $item = self::servicios()[$slug] ?? null;

        return $item ? ['slug' => $slug] + $item : null;
    }

    public static function municipio(string $slug): ?array
    {
        $item = self::municipios()[$slug] ?? null;

        return $item ? ['slug' => $slug, 'short' => $item['short'] ?? $item['name']] + $item : null;
    }

    public static function enfoque(string $servicio, string $municipio): ?string
    {
        return config("propiedad-horizontal.enfoque.{$servicio}.{$municipio}");
    }

    public static function paquete(?string $key): ?array
    {
        return $key ? config("propiedad-horizontal.paquetes.{$key}") : null;
    }

    public static function path(?string $servicio = null, ?string $municipio = null): string
    {
        return rtrim(self::BASE.'/'.($servicio ?? '').($municipio ? '/'.$municipio : ''), '/');
    }

    public static function image(array $servicio): string
    {
        $url = config('images.services.'.($servicio['image_service'] ?? 'camaras-4k'))
            ?? config('images.services.camaras-4k');

        if (is_string($url) && ! str_starts_with($url, 'http')) {
            return asset(ltrim($url, '/'));
        }

        return (string) $url;
    }

    /**
     * Todas las URLs del nicho (para el sitemap).
     *
     * @return array<int, array{path: string, priority: string}>
     */
    public static function allPaths(): array
    {
        $paths = [
            ['path' => self::BASE, 'priority' => '0.9'],
            ['path' => self::DIAGNOSTICO, 'priority' => '0.9'],
        ];

        foreach (array_keys(self::servicios()) as $servicio) {
            $paths[] = ['path' => self::path($servicio), 'priority' => '0.8'];

            foreach (array_keys(self::municipios()) as $municipio) {
                $paths[] = ['path' => self::path($servicio, $municipio), 'priority' => '0.7'];
            }
        }

        return $paths;
    }

    /**
     * Proyectos reales del portafolio que coinciden con el servicio y/o municipio.
     */
    public static function proyectos(?array $servicio = null, ?array $municipio = null, int $limit = 3): Collection
    {
        try {
            $query = Project::query()
                ->with(['images' => fn ($q) => $q->orderByDesc('is_cover')->orderBy('sort_order')])
                ->latest();

            // Solo proyectos de propiedad horizontal (conjuntos, unidades, edificios).
            $query->where(function ($q) {
                foreach (['conjunto', 'unidad residencial', 'urbanizaci', 'edificio', 'torre'] as $term) {
                    $q->orWhere('title', 'like', '%'.$term.'%')
                        ->orWhere('description', 'like', '%'.$term.'%');
                }
            });

            $slugs = $servicio['related_slugs'] ?? [];
            if ($slugs !== []) {
                $ids = Service::query()->whereIn('slug', $slugs)->pluck('id');
                $query->whereIn('service_id', $ids);
            }

            if ($municipio) {
                $terms = $municipio['match'] ?? [mb_strtolower($municipio['name'])];
                $query->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $like = '%'.$term.'%';
                        $q->orWhere('location', 'like', $like)
                            ->orWhere('address', 'like', $like)
                            ->orWhere('barrio', 'like', $like);
                    }
                });
            }

            return $query->limit($limit)->get();
        } catch (Throwable) {
            return collect();
        }
    }

    /* ─────────────── Schema.org ─────────────── */

    /**
     * @param  array<int, array{0: string, 1: string}>  $crumbs  [nombre, path]
     */
    public static function breadcrumbGraph(array $crumbs): array
    {
        $items = [];
        foreach (array_values($crumbs) as $i => [$name, $path]) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $name,
                'item' => Seo::url($path),
            ];
        }

        return ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    public static function serviceGraph(string $name, string $description, string $path, ?array $municipio = null): array
    {
        $area = $municipio
            ? [['@type' => 'City', 'name' => $municipio['name']]]
            : array_map(
                static fn (array $m) => ['@type' => 'City', 'name' => $m['short'] ?? $m['name']],
                array_values(self::municipios())
            );

        return [
            '@type' => 'Service',
            'name' => $name,
            'serviceType' => $name,
            'description' => $description,
            'url' => Seo::url($path),
            'audience' => [
                '@type' => 'BusinessAudience',
                'audienceType' => 'Unidades y conjuntos residenciales, administradores de propiedad horizontal',
            ],
            'provider' => ['@id' => Seo::url('/').'#business'],
            'areaServed' => $area,
        ];
    }
}
