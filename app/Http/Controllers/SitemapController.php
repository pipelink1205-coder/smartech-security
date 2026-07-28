<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $base = rtrim(config('app.url'), '/');

        $urls = [
            ['loc' => $base.'/', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => $base.'/servicios', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => $base.'/proyectos', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['loc' => $base.'/contacto', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => $base.'/privacidad', 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        foreach (Service::active()->ordered()->get(['slug', 'updated_at']) as $service) {
            $urls[] = [
                'loc' => $base.'/servicios/'.$service->slug,
                'lastmod' => $service->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => $service->slug === 'outsourcing-ti' || $service->slug === 'iptv-hoteles'
                    ? '0.9'
                    : '0.7',
            ];
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
