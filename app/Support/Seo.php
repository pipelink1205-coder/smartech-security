<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Support\Str;

class Seo
{
    public static function siteUrl(): string
    {
        $base = rtrim((string) config('app.url'), '/');

        $host = parse_url($base, PHP_URL_HOST) ?: '';

        if (
            $base === ''
            || str_contains($base, 'localhost')
            || str_contains($base, '127.0.0.1')
            || preg_match('/\.(test|local)$/', $host)
        ) {
            return 'https://smarttechsecurity.com.co';
        }

        return $base;
    }

    public static function url(string $path = '/'): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            if (str_contains($path, 'localhost') || str_contains($path, '127.0.0.1')) {
                $parts = parse_url($path);
                $rewritten = ($parts['path'] ?? '/').(isset($parts['query']) ? '?'.$parts['query'] : '');

                return self::siteUrl().$rewritten;
            }

            return $path;
        }

        $normalized = '/'.ltrim($path, '/');

        return $normalized === '/' ? self::siteUrl().'/' : self::siteUrl().$normalized;
    }

    /**
     * @return array{title: string, description: string}
     */
    public static function page(string $key): array
    {
        $page = config('seo.pages.'.$key);

        if (! is_array($page)) {
            return [
                'title' => (string) config('seo.defaults.title'),
                'description' => (string) config('seo.defaults.description'),
            ];
        }

        return [
            'title' => (string) ($page['title'] ?? config('seo.defaults.title')),
            'description' => (string) ($page['description'] ?? config('seo.defaults.description')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function localBusinessGraph(): array
    {
        $social = array_values(array_filter(array_map(
            static fn ($url) => is_string($url) && $url !== '' ? $url : null,
            config('contact.social', [])
        )));

        $graph = [
            '@type' => 'LocalBusiness',
            '@id' => self::url('/').'#business',
            'name' => 'Smart Tech Security',
            'alternateName' => 'SmartTech Security',
            'url' => self::url('/'),
            'image' => self::url('/images/logo-transparent.png'),
            'logo' => self::url('/images/logo-transparent.png'),
            'description' => config('seo.defaults.description'),
            'email' => config('contact.email'),
            'slogan' => 'Un mundo de soluciones tecnológicas',
            'priceRange' => '$$',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Carrera 31 #39 Sur 20',
                'addressLocality' => 'Envigado',
                'addressRegion' => 'Antioquia',
                'addressCountry' => 'CO',
            ],
            'areaServed' => [
                ['@type' => 'City', 'name' => 'Medellín'],
                ['@type' => 'City', 'name' => 'Envigado'],
                ['@type' => 'AdministrativeArea', 'name' => 'Valle de Aburrá'],
            ],
        ];

        $phone = preg_replace('/\D/', '', (string) config('contact.whatsapp'));
        if ($phone !== '') {
            $graph['telephone'] = '+'.$phone;
        }

        if ($social !== []) {
            $graph['sameAs'] = $social;
        }

        return $graph;
    }

    /**
     * @return array<string, mixed>
     */
    public static function websiteGraph(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::url('/').'#website',
            'url' => self::url('/'),
            'name' => 'Smart Tech Security',
            'inLanguage' => 'es-CO',
            'publisher' => [
                '@id' => self::url('/').'#business',
            ],
        ];
    }

    /**
     * @param  array<int, array<string, string>>|null  $items
     * @return array<string, mixed>|null
     */
    public static function faqGraph(?array $items = null): ?array
    {
        $items ??= config('site.faq', []);

        $entities = [];
        foreach ($items as $item) {
            $question = trim((string) ($item['q'] ?? $item['question'] ?? ''));
            $answer = trim((string) ($item['a'] ?? $item['answer'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $entities[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        if ($entities === []) {
            return null;
        }

        return [
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function serviceGraph(Service $service, ?string $description = null): array
    {
        $text = $description ?: strip_tags((string) $service->description);

        return [
            '@type' => 'Service',
            'name' => $service->name,
            'description' => Str::limit(preg_replace('/\s+/', ' ', $text) ?? $text, 180, ''),
            'url' => self::url('/servicios/'.$service->slug),
            'image' => self::url($service->image_url),
            'provider' => [
                '@id' => self::url('/').'#business',
            ],
            'areaServed' => [
                ['@type' => 'City', 'name' => 'Medellín'],
                ['@type' => 'AdministrativeArea', 'name' => 'Valle de Aburrá'],
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>|null>  $nodes
     * @return array<string, mixed>
     */
    public static function graph(array $nodes): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter($nodes)),
        ];
    }
}
