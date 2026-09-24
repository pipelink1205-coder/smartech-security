<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\Seo;
use Illuminate\Http\Response;
use Throwable;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $base = Seo::siteUrl();

        $lastmod = static function (string $view): ?string {
            $path = resource_path('views/'.$view);

            return is_file($path) ? date('c', filemtime($path)) : null;
        };

        $urls = [
            ['loc' => $base.'/', 'lastmod' => $lastmod('welcome.blade.php'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => $base.'/servicios', 'lastmod' => $lastmod('pages/servicios.blade.php'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => $base.'/proyectos', 'lastmod' => $lastmod('pages/proyectos.blade.php'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => $base.'/contacto', 'lastmod' => $lastmod('pages/contacto.blade.php'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $base.'/privacidad', 'lastmod' => $lastmod('pages/privacidad.blade.php'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $base.'/terminos-y-condiciones', 'lastmod' => $lastmod('pages/terminos.blade.php'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => $base.'/politica-de-ventas', 'lastmod' => $lastmod('pages/politica-ventas.blade.php'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        try {
            foreach (Service::query()->where('is_active', true)->orderBy('order')->get(['slug', 'updated_at']) as $service) {
                $urls[] = [
                    'loc' => $base.'/servicios/'.$service->slug,
                    'lastmod' => optional($service->updated_at)->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => in_array($service->slug, ['outsourcing-ti', 'iptv-hoteles', 'desarrollo-software'], true)
                        ? '0.9'
                        : '0.7',
                ];
            }
        } catch (Throwable) {
            // Si la BD falla, devolvemos al menos las páginas fijas.
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
