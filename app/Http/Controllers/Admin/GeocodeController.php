<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeocodeController extends Controller
{
    /** Valle de Aburrá (Medellín y alrededores) */
    private const VIEWBOX = '-75.72,6.05,-75.42,6.45';

    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(['message' => 'Indica una dirección para buscar.'], 422);
        }

        $attempts = $this->buildQueries($query);

        foreach ($attempts as $attemptQuery) {
            $hit = $this->nominatimSearch($attemptQuery);
            if ($hit !== null) {
                return response()->json([
                    'source' => 'nominatim',
                    'lat' => $hit['lat'],
                    'lng' => $hit['lng'],
                    'label' => $hit['label'],
                    'query_used' => $attemptQuery,
                ]);
            }
        }

        $photon = $this->photonSearch($query);
        if ($photon !== null) {
            return response()->json([
                'source' => 'photon',
                'lat' => $photon['lat'],
                'lng' => $photon['lng'],
                'label' => $photon['label'],
                'query_used' => $query,
            ]);
        }

        return response()->json([
            'message' => 'No se encontró una ubicación fiable. Coloca el pin manualmente en el mapa.',
        ], 404);
    }

    /** @return list<string> */
    private function buildQueries(string $query): array
    {
        $normalized = preg_replace('/\s+/', ' ', $query) ?? $query;
        $lower = mb_strtolower($normalized);

        $hasCountry = str_contains($lower, 'colombia');
        $hasRegion = str_contains($lower, 'antioquia')
            || str_contains($lower, 'medellín')
            || str_contains($lower, 'medellin')
            || str_contains($lower, 'envigado')
            || str_contains($lower, 'bello')
            || str_contains($lower, 'itagüí')
            || str_contains($lower, 'itagui');

        $queries = [$normalized];

        if (! $hasRegion) {
            $queries[] = "{$normalized}, Medellín, Antioquia, Colombia";
        }

        if (! $hasCountry) {
            $queries[] = "{$normalized}, Antioquia, Colombia";
        }

        return array_values(array_unique($queries));
    }

    /** @return array{lat: float, lng: float, label: string}|null */
    private function nominatimSearch(string $query): ?array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders($this->geocoderHeaders())
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'jsonv2',
                    'limit' => 5,
                    'countrycodes' => 'co',
                    'viewbox' => self::VIEWBOX,
                    'bounded' => 0,
                    'addressdetails' => 1,
                    'q' => $query,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $results = $response->json();
            if (! is_array($results) || $results === []) {
                return null;
            }

            $best = $this->pickBestResult($results);

            return [
                'lat' => (float) $best['lat'],
                'lng' => (float) $best['lon'],
                'label' => (string) ($best['display_name'] ?? $query),
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /** @return array{lat: float, lng: float, label: string}|null */
    private function photonSearch(string $query): ?array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://photon.komoot.io/api/', [
                    'q' => $query,
                    'limit' => 5,
                    'lat' => 6.2442,
                    'lon' => -75.5812,
                    'lang' => 'es',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $features = $response->json('features');
            if (! is_array($features) || $features === []) {
                return null;
            }

            foreach ($features as $feature) {
                $coords = $feature['geometry']['coordinates'] ?? null;
                if (! is_array($coords) || count($coords) < 2) {
                    continue;
                }

                $props = $feature['properties'] ?? [];
                $country = strtoupper((string) ($props['country'] ?? ''));
                if ($country !== '' && $country !== 'CO' && $country !== 'COLOMBIA') {
                    continue;
                }

                $label = collect([
                    $props['name'] ?? null,
                    $props['street'] ?? null,
                    $props['city'] ?? $props['county'] ?? null,
                    $props['state'] ?? null,
                ])->filter()->unique()->implode(', ');

                return [
                    'lat' => (float) $coords[1],
                    'lng' => (float) $coords[0],
                    'label' => $label !== '' ? $label : $query,
                ];
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    /** @param list<array<string, mixed>> $results */
    private function pickBestResult(array $results): array
    {
        usort($results, function (array $a, array $b): int {
            $scoreA = $this->resultScore($a);
            $scoreB = $this->resultScore($b);

            return $scoreB <=> $scoreA;
        });

        return $results[0];
    }

    /** @param array<string, mixed> $result */
    private function resultScore(array $result): int
    {
        $type = (string) ($result['type'] ?? '');
        $class = (string) ($result['class'] ?? '');
        $importance = (float) ($result['importance'] ?? 0);

        $score = (int) round($importance * 100);

        if (in_array($type, ['house', 'building', 'residential', 'address'], true)) {
            $score += 40;
        }

        if ($class === 'highway') {
            $score += 10;
        }

        if ($class === 'place' && in_array($type, ['city', 'town', 'suburb', 'neighbourhood'], true)) {
            $score -= 20;
        }

        return $score;
    }

    /** @return array<string, string> */
    private function geocoderHeaders(): array
    {
        $email = config('contact.admin_email', 'seguridadsmarttech@gmail.com');

        return [
            'Accept' => 'application/json',
            'User-Agent' => 'SmartTechSecurity/1.0 (admin geocode; '.$email.')',
        ];
    }
}
