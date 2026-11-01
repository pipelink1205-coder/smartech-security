<?php

namespace App\Http\Controllers;

use App\Support\PropiedadHorizontal as PH;

/**
 * Landings SEO del nicho Propiedad Horizontal:
 *  /unidades-residenciales                         → hub
 *  /unidades-residenciales/diagnostico-gratuito    → landing del diagnóstico
 *  /unidades-residenciales/{servicio}              → servicio
 *  /unidades-residenciales/{servicio}/{municipio}  → servicio × municipio
 *  /conjuntos-residenciales/... → 301 a /unidades-residenciales/...
 */
class PropiedadHorizontalController extends Controller
{
    public function hub()
    {
        return view('pages.ph.hub', [
            'servicios' => PH::servicios(),
            'municipios' => PH::municipios(),
            'proyectos' => PH::proyectos(limit: 3),
        ]);
    }

    public function servicio(string $servicio)
    {
        $item = PH::servicio($servicio);
        abort_unless($item, 404);

        return view('pages.ph.servicio', [
            'servicio' => $item,
            'municipios' => PH::municipios(),
            'paquete' => PH::paquete($item['paquete'] ?? null),
            'otros' => array_diff_key(PH::servicios(), [$servicio => true]),
            'proyectos' => PH::proyectos($item, limit: 3),
        ]);
    }

    public function local(string $servicio, string $municipio)
    {
        $item = PH::servicio($servicio);
        $mun = PH::municipio($municipio);
        abort_unless($item && $mun, 404);

        $vecinos = collect($mun['vecinos'] ?? [])
            ->map(fn (string $slug) => PH::municipio($slug))
            ->filter()
            ->values();

        return view('pages.ph.local', [
            'servicio' => $item,
            'municipio' => $mun,
            'enfoque' => PH::enfoque($servicio, $municipio),
            'paquete' => PH::paquete($item['paquete'] ?? null),
            'otros' => array_diff_key(PH::servicios(), [$servicio => true]),
            'vecinos' => $vecinos,
            'proyectos' => PH::proyectos($item, $mun, 3),
        ]);
    }

    public function diagnostico()
    {
        return view('pages.ph.diagnostico', [
            'municipios' => PH::municipios(),
        ]);
    }
}
