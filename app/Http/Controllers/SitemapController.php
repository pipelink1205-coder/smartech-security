<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Response;
use Throwable;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $base = rtrim((string) config('app.url'), '/');

        // Si APP_URL está mal en el servidor, no servir URLs de localhost a Google.
        if ($base === '' || str_contains($base, 'localhost') || str_contains($base, '127.0.0.1')) {
            $base = 'https://smarttechsecurity.com.co';
        }

        $urls = [
            ['loc' => $base.'/', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => $base.'/servicios', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => $base.'/proyectos', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => $base.'/contacto', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $base.'/privacidad', 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        try {
            foreach (Service::query()->where('is_active', true)->orderBy('order')->get(['slug', 'updated_at']) as $service) {
                $urls[] = [
                    'loc' => $base.'/servicios/'.$service->slug,
                    'lastmod' => optional($service->updated_at)->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => in_array($service->slug, ['outsourcing-ti', 'iptv-hoteles'], true)
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
