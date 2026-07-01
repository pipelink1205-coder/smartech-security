<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Geocodificación para el panel admin.
 *
 * Dos "gestores" que se validan entre sí:
 *   1) Geocodificador (dirección → coordenadas): Alcaldía de Medellín → ArcGIS → Nominatim.
 *   2) Espacial (coordenadas → comuna): punto-en-polígono sobre comunas-medellin.geojson.
 *
 * La comuna final se toma del polígono cuando el punto cae en Medellín, y se
 * reporta si coincide con la que devolvió el geocodificador (validación cruzada).
 */
class GeocodeController extends Controller
{
    /** Valle de Aburrá (Medellín y alrededores) */
    private const VIEWBOX = '-75.72,6.05,-75.42,6.45';

    private const ALCALDIA_API = 'https://www.medellin.gov.co/statements/api/innovaciondigital/geocodificacion/';

    /** Municipios del Valle de Aburrá: clave normalizada => nombre para mostrar. */
    private const VALLE_MUNICIPIOS = [
        'medellin'    => 'Medellín',
        'bello'       => 'Bello',
        'itagui'      => 'Itagüí',
        'envigado'    => 'Envigado',
        'sabaneta'    => 'Sabaneta',
        'la estrella' => 'La Estrella',
        'caldas'      => 'Caldas',
        'copacabana'  => 'Copacabana',
        'girardota'   => 'Girardota',
        'barbosa'     => 'Barbosa',
    ];

    /** @var array<int, array<string, mixed>>|null */
    private static ?array $comunasCache = null;

    public function __invoke(Request $request): JsonResponse
    {
        if ($request->filled('lat') && $request->filled('lng')) {
            return $this->reverse((float) $request->query('lat'), (float) $request->query('lng'));
        }

        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(['message' => 'Indica una dirección para buscar.'], 422);
        }

        return $this->forward($query);
    }

    /**
     * Busca la dirección en el Valle de Aburrá (Medellín + municipios vecinos).
     * Si aparece en varios municipios, marca la respuesta como ambigua.
     */
    private function forward(string $query): JsonResponse
    {
        $municipios = $this->collectValleCandidates($query);

        if ($municipios !== []) {
            $keys = array_keys($municipios);
            $primaryKey = in_array('Medellín', $keys, true) ? 'Medellín' : $keys[0];

            $payload = $this->withZoneValidation($municipios[$primaryKey]['hit']);
            $payload['ambiguous'] = count($municipios) > 1;
            $payload['municipios'] = $keys;
            $payload['candidates'] = array_values(array_map(static fn (array $c): array => [
                'municipio' => $c['municipio'],
                'lat'       => $c['hit']['lat'],
                'lng'       => $c['hit']['lng'],
                'source'    => $c['hit']['source'],
            ], $municipios));

            return response()->json($payload);
        }

        // Fuera del Valle de Aburrá: geocodificador general.
        $hit = $this->arcgisSearch($query)
            ?? $this->nominatimForward($query)
            ?? $this->photonSearch($query);

        if ($hit === null) {
            return response()->json([
                'message' => 'No se encontró una ubicación fiable. Coloca el pin manualmente en el mapa.',
            ], 404);
        }

        $payload = $this->withZoneValidation($hit);
        $payload['ambiguous'] = false;
        $payload['municipios'] = array_values(array_filter([$hit['municipio'] ?? null]));
        $payload['candidates'] = [[
            'municipio' => $hit['municipio'] ?? null,
            'lat'       => $hit['lat'],
            'lng'       => $hit['lng'],
            'source'    => $hit['source'],
        ]];

        return response()->json($payload);
    }

    /**
     * Reúne un candidato por municipio del Valle de Aburrá.
     * Medellín viene de la API de la Alcaldía; el resto, de Nominatim acotado al Valle.
     *
     * @return array<string, array{municipio: string, hit: array<string, mixed>}>
     */
    private function collectValleCandidates(string $query): array
    {
        $out = [];

        $alcaldia = $this->alcaldiaSearch($query);
        if ($alcaldia !== null) {
            $out['Medellín'] = ['municipio' => 'Medellín', 'hit' => $alcaldia];
        }

        foreach ($this->nominatimValleList($query) as $candidate) {
            $display = $this->canonicalMunicipio($candidate['municipio'] ?? null);
            if ($display === null) {
                continue;
            }
            if ($display === 'Medellín' && isset($out['Medellín'])) {
                continue;
            }
            if (! isset($out[$display])) {
                $candidate['municipio'] = $display;
                $out[$display] = ['municipio' => $display, 'hit' => $candidate];
            }
        }

        return $out;
    }

    /**
     * Reverse: coordenadas → comuna (polígono) + barrio (Nominatim).
     */
    private function reverse(float $lat, float $lng): JsonResponse
    {
        $comuna = $this->comunaFromPolygon($lat, $lng);
        $barrio = $this->nominatimBarrio($lat, $lng);

        return response()->json([
            'source'        => 'polygon',
            'lat'           => $lat,
            'lng'           => $lng,
            'comuna_numero' => $comuna['numero'] ?? null,
            'comuna_nombre' => $comuna['nombre'] ?? null,
            'barrio'        => $barrio,
            'municipio'     => $comuna !== null ? 'Medellín' : null,
            'in_medellin'   => $comuna !== null,
            'confidence'    => $comuna !== null ? 'alta' : 'baja',
            'cross_check'   => ['geocoder' => null, 'polygon' => $comuna['numero'] ?? null, 'match' => null],
        ]);
    }

    /**
     * Combina el resultado del geocodificador con la comuna del polígono y
     * reporta si ambas coinciden (validación cruzada).
     *
     * @param  array<string, mixed>  $hit
     * @return array<string, mixed>
     */
    private function withZoneValidation(array $hit): array
    {
        $lat = (float) $hit['lat'];
        $lng = (float) $hit['lng'];

        $polygon = $this->comunaFromPolygon($lat, $lng);
        $geocoderComuna = $hit['comuna_numero'] ?? null;
        $polygonComuna = $polygon['numero'] ?? null;

        $match = ($geocoderComuna !== null && $polygonComuna !== null)
            ? ($geocoderComuna === $polygonComuna)
            : null;

        $confidence = $hit['confidence'] ?? 'media';
        if ($match === false) {
            $confidence = 'media';
        }

        return [
            'source'        => $hit['source'],
            'lat'           => $lat,
            'lng'           => $lng,
            'label'         => $hit['label'] ?? null,
            'comuna_numero' => $polygonComuna ?? $geocoderComuna,
            'comuna_nombre' => $polygon['nombre'] ?? ($hit['comuna_nombre'] ?? null),
            'barrio'        => $hit['barrio'] ?? $this->nominatimBarrio($lat, $lng),
            'municipio'     => $polygonComuna !== null ? 'Medellín' : ($hit['municipio'] ?? null),
            'in_medellin'   => $polygonComuna !== null,
            'confidence'    => $confidence,
            'cross_check'   => [
                'geocoder' => $geocoderComuna,
                'polygon'  => $polygonComuna,
                'match'    => $match,
            ],
        ];
    }

    // -----------------------------------------------------------------
    //  Gestor 1 — Geocodificadores (dirección → coordenadas)
    // -----------------------------------------------------------------

    /**
     * API oficial de la Alcaldía de Medellín (Planeación / catastro).
     * Devuelve el punto exacto de la nomenclatura, comuna y barrio oficiales.
     *
     * @return array<string, mixed>|null
     */
    private function alcaldiaSearch(string $query): ?array
    {
        if (mb_strlen($query) < 3) {
            return null;
        }

        try {
            $response = Http::timeout(12)
                ->withHeaders(['Accept' => 'application/json', 'User-Agent' => 'SmartTechSecurity/1.0'])
                ->get(self::ALCALDIA_API . rawurlencode($query));

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            $item = $data['item'][0] ?? null;

            if (empty($data['status']) || ! is_array($item)) {
                return null;
            }

            $lat = (float) ($item['latitud'] ?? 0);
            $lng = (float) ($item['longitud'] ?? 0);

            if (! $lat || ! $lng) {
                return null;
            }

            $codigo = $item['codigo_comuna_pla'] ?? $item['codigo_comuna_cat'] ?? null;
            $barrio = $item['nombre_barrio_cat'] ?? $item['nombre_barrio_pla'] ?? null;

            return [
                'source'        => 'alcaldia',
                'lat'           => $lat,
                'lng'           => $lng,
                'label'         => $item['dir_encasillada'] ?? $item['dir'] ?? $query,
                'comuna_numero' => $codigo !== null ? (int) ltrim((string) $codigo, '0') : null,
                'comuna_nombre' => $item['nombre_comuna_cat'] ?? $item['nombre_comuna_pla'] ?? null,
                'barrio'        => $barrio ? mb_strtoupper((string) $barrio) : null,
                'municipio'     => 'Medellín',
                'confidence'    => 'alta',
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Respaldo: Esri World GeocodeServer.
     *
     * @return array<string, mixed>|null
     */
    private function arcgisSearch(string $query): ?array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates', [
                    'f'            => 'json',
                    'SingleLine'   => $this->withMedellinContext($query),
                    'outFields'    => '*',
                    'maxLocations' => 5,
                    'countryCode'  => 'CO',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $candidates = $response->json('candidates');
            $best = is_array($candidates) ? ($candidates[0] ?? null) : null;
            $loc = $best['location'] ?? null;

            if (! is_array($loc) || ! isset($loc['x'], $loc['y'])) {
                return null;
            }

            $score = (float) ($best['score'] ?? 0);

            return [
                'source'        => 'arcgis',
                'lat'           => (float) $loc['y'],
                'lng'           => (float) $loc['x'],
                'label'         => $best['address'] ?? $query,
                'comuna_numero' => null,
                'comuna_nombre' => null,
                'barrio'        => null,
                'municipio'     => null,
                'confidence'    => $score >= 90 ? 'alta' : ($score >= 70 ? 'media' : 'baja'),
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Respaldo: Nominatim (OpenStreetMap).
     *
     * @return array<string, mixed>|null
     */
    private function nominatimForward(string $query): ?array
    {
        foreach ($this->buildQueries($query) as $attempt) {
            $hit = $this->nominatimSearch($attempt);
            if ($hit !== null) {
                return $hit;
            }
        }

        return null;
    }

    /** @return array<string, mixed>|null */
    private function nominatimSearch(string $query): ?array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders($this->geocoderHeaders())
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format'         => 'jsonv2',
                    'limit'          => 5,
                    'countrycodes'   => 'co',
                    'viewbox'        => self::VIEWBOX,
                    'bounded'        => 0,
                    'addressdetails' => 1,
                    'q'              => $query,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $results = $response->json();
            if (! is_array($results) || $results === []) {
                return null;
            }

            $best = $this->pickBestResult($results);
            $address = $best['address'] ?? [];

            return [
                'source'        => 'nominatim',
                'lat'           => (float) $best['lat'],
                'lng'           => (float) $best['lon'],
                'label'         => (string) ($best['display_name'] ?? $query),
                'comuna_numero' => null,
                'comuna_nombre' => null,
                'barrio'        => $this->barrioFromNominatimAddress($address),
                'municipio'     => $address['city'] ?? $address['town'] ?? $address['municipality'] ?? null,
                'confidence'    => ((float) ($best['importance'] ?? 0)) >= 0.5 ? 'media' : 'baja',
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Candidatos de Nominatim acotados al Valle de Aburrá (uno por municipio).
     *
     * @return list<array<string, mixed>>
     */
    private function nominatimValleList(string $query): array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders($this->geocoderHeaders())
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format'         => 'jsonv2',
                    'limit'          => 10,
                    'countrycodes'   => 'co',
                    'viewbox'        => self::VIEWBOX,
                    'bounded'        => 1,
                    'addressdetails' => 1,
                    'q'              => $query,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $results = $response->json();
            if (! is_array($results)) {
                return [];
            }

            $candidates = [];
            foreach ($results as $result) {
                if (! isset($result['lat'], $result['lon'])) {
                    continue;
                }

                $address = $result['address'] ?? [];

                $candidates[] = [
                    'source'        => 'nominatim',
                    'lat'           => (float) $result['lat'],
                    'lng'           => (float) $result['lon'],
                    'label'         => (string) ($result['display_name'] ?? $query),
                    'comuna_numero' => null,
                    'comuna_nombre' => null,
                    'barrio'        => $this->barrioFromNominatimAddress($address),
                    'municipio'     => $address['city'] ?? $address['town'] ?? $address['municipality'] ?? $address['village'] ?? null,
                    'confidence'    => ((float) ($result['importance'] ?? 0)) >= 0.5 ? 'media' : 'baja',
                ];
            }

            return $candidates;
        } catch (\Throwable) {
            return [];
        }
    }

    /** Devuelve el nombre canónico del municipio si pertenece al Valle de Aburrá, o null. */
    private function canonicalMunicipio(?string $name): ?string
    {
        if ($name === null || trim($name) === '') {
            return null;
        }

        $normalized = mb_strtolower(trim($name));
        $normalized = strtr($normalized, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u']);

        return self::VALLE_MUNICIPIOS[$normalized] ?? null;
    }

    /** @return array<string, mixed>|null */
    private function photonSearch(string $query): ?array
    {
        try {
            $response = Http::timeout(12)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://photon.komoot.io/api/', [
                    'q'     => $query,
                    'limit' => 5,
                    'lat'   => 6.2442,
                    'lon'   => -75.5812,
                    'lang'  => 'es',
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
                    'source'        => 'photon',
                    'lat'           => (float) $coords[1],
                    'lng'           => (float) $coords[0],
                    'label'         => $label !== '' ? $label : $query,
                    'comuna_numero' => null,
                    'comuna_nombre' => null,
                    'barrio'        => null,
                    'municipio'     => $props['city'] ?? $props['county'] ?? null,
                    'confidence'    => 'baja',
                ];
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    // -----------------------------------------------------------------
    //  Gestor 2 — Espacial (coordenadas → comuna por polígono)
    // -----------------------------------------------------------------

    /**
     * Comuna/corregimiento oficial que contiene el punto.
     *
     * @return array{numero: int|null, nombre: string|null}|null
     */
    private function comunaFromPolygon(float $lat, float $lng): ?array
    {
        foreach ($this->loadComunas() as $feature) {
            $geometry = $feature['geometry'] ?? null;
            if ($geometry && $this->geoContainsPoint($lat, $lng, $geometry)) {
                $props = $feature['properties'] ?? [];
                $numero = isset($props['numero']) ? (int) $props['numero'] : null;

                return [
                    'numero' => $numero ?: null,
                    'nombre' => $props['nombre'] ?? $props['org_key'] ?? null,
                ];
            }
        }

        return null;
    }

    /** @return array<int, array<string, mixed>> */
    private function loadComunas(): array
    {
        if (self::$comunasCache !== null) {
            return self::$comunasCache;
        }

        $path = public_path('data/comunas-medellin.geojson');

        if (! is_readable($path)) {
            return self::$comunasCache = [];
        }

        $json = json_decode((string) file_get_contents($path), true);
        $features = $json['features'] ?? [];

        return self::$comunasCache = is_array($features) ? $features : [];
    }

    /** @param array<string, mixed> $geometry */
    private function geoContainsPoint(float $lat, float $lng, array $geometry): bool
    {
        $type = $geometry['type'] ?? null;
        $coordinates = $geometry['coordinates'] ?? null;

        if (! is_array($coordinates)) {
            return false;
        }

        if ($type === 'Polygon') {
            return $this->polygonContains($lat, $lng, $coordinates);
        }

        if ($type === 'MultiPolygon') {
            foreach ($coordinates as $polygon) {
                if (is_array($polygon) && $this->polygonContains($lat, $lng, $polygon)) {
                    return true;
                }
            }
        }

        return false;
    }

    /** @param array<int, mixed> $rings */
    private function polygonContains(float $lat, float $lng, array $rings): bool
    {
        if (! isset($rings[0]) || ! is_array($rings[0]) || ! $this->ringContains($lat, $lng, $rings[0])) {
            return false;
        }

        for ($h = 1; $h < count($rings); $h++) {
            if (is_array($rings[$h]) && $this->ringContains($lat, $lng, $rings[$h])) {
                return false; // punto dentro de un hueco
            }
        }

        return true;
    }

    /** @param array<int, mixed> $ring */
    private function ringContains(float $lat, float $lng, array $ring): bool
    {
        $count = count($ring);
        if ($count < 3) {
            return false;
        }

        $inside = false;
        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = (float) $ring[$i][0];
            $yi = (float) $ring[$i][1];
            $xj = (float) $ring[$j][0];
            $yj = (float) $ring[$j][1];

            $denom = ($yj - $yi) ?: 1e-12;
            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / $denom + $xi);

            if ($intersect) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    private function nominatimBarrio(float $lat, float $lng): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->geocoderHeaders())
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format'         => 'jsonv2',
                    'lat'            => $lat,
                    'lon'            => $lng,
                    'zoom'           => 18,
                    'addressdetails' => 1,
                ]);

            if (! $response->successful()) {
                return null;
            }

            return $this->barrioFromNominatimAddress($response->json('address') ?? []);
        } catch (\Throwable) {
            return null;
        }
    }

    /** @param array<string, mixed> $address */
    private function barrioFromNominatimAddress(array $address): ?string
    {
        $city = mb_strtolower((string) ($address['city'] ?? $address['town'] ?? $address['municipality'] ?? ''));

        foreach (['neighbourhood', 'quarter', 'suburb', 'hamlet'] as $key) {
            $value = trim((string) ($address[$key] ?? ''));
            if ($value === '') {
                continue;
            }
            $lower = mb_strtolower($value);
            if ($lower === $city || in_array($lower, ['medellín', 'medellin'], true)) {
                continue;
            }
            if (preg_match('/comuna\s*\d+|corregimiento/i', $value)) {
                continue;
            }

            return mb_strtoupper($value);
        }

        return null;
    }

    // -----------------------------------------------------------------
    //  Helpers
    // -----------------------------------------------------------------

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

    private function withMedellinContext(string $query): string
    {
        $lower = mb_strtolower($query);
        if (str_contains($lower, 'medellín') || str_contains($lower, 'medellin') || str_contains($lower, 'colombia')) {
            return $query;
        }

        return "{$query}, Medellín, Antioquia, Colombia";
    }

    /** @param list<array<string, mixed>> $results */
    private function pickBestResult(array $results): array
    {
        usort($results, fn (array $a, array $b): int => $this->resultScore($b) <=> $this->resultScore($a));

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
            'Accept'          => 'application/json',
            'Accept-Language' => 'es',
            'User-Agent'      => 'SmartTechSecurity/1.0 (admin geocode; ' . $email . ')',
        ];
    }
}
