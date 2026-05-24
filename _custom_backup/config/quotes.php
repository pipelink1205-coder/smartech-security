<?php

/**
 * Precios base por servicio (en COP)
 * Modifica estos valores según tu estructura de costos real.
 * min → precio base mínimo estimado
 * max → precio base máximo estimado
 */
return [
    'pricing' => [
        'Cámaras de Seguridad 4K' => [800_000,   3_500_000],
        'Energía Solar'            => [4_000_000, 20_000_000],
        'IPTV para Hoteles'        => [2_000_000, 12_000_000],
        'Domótica & Smart Home'    => [3_000_000, 15_000_000],
        'Cerraduras Digitales'     => [600_000,   2_500_000],
        'Redes & Conectividad'     => [1_200_000, 6_000_000],
        'Varios servicios'         => [2_000_000, 25_000_000],
    ],

    // Zonas con recargo por distancia (%)
    'zone_surcharge' => [
        'Medellín'   => 0,
        'El Poblado' => 0,
        'Laureles'   => 0,
        'Envigado'   => 5,
        'Bello'      => 5,
        'Itagüí'     => 5,
        'Sabaneta'   => 8,
        'La Estrella' => 8,
        'Caldas'     => 12,
        'Copacabana' => 10,
    ],
];
