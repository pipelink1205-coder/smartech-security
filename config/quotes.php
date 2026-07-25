<?php

return [
    'pricing' => [
        'Cámaras de Seguridad 4K'      => [800_000,   3_500_000],
        'Energía Solar Fotovoltaica'   => [4_000_000, 20_000_000],
        'Control de Acceso Biométrico' => [1_500_000, 8_000_000],
        'Alarmas de Seguridad'         => [900_000,   4_000_000],
        'Domótica y Casas Inteligentes'=> [3_000_000, 15_000_000],
        'Fibra Óptica y Redes'         => [1_200_000, 6_000_000],
        'IPTV para Hoteles'            => [2_000_000, 12_000_000],
        'Video Porteros y Citofonía IP' => [1_200_000, 6_000_000],
        'Control de Acceso Vehicular'  => [2_500_000, 12_000_000],
        'Enlaces Inalámbricos'         => [1_800_000, 9_000_000],
        'Sistemas de Detección de Incendios' => [2_000_000, 10_000_000],
        'Outsourcing de TI'            => [900_000,   6_500_000],
        'Varios servicios'             => [2_000_000, 25_000_000],
    ],

    /*
     * Outsourcing de TI — planes y supuestos del comparador de ahorro.
     * Los precios son "desde" en COP. El factor prestacional (~1.53) cubre
     * salud, pensión, ARL, cesantías e intereses, prima, vacaciones y parafiscales.
     */
    'it' => [
        'payroll_factor' => 1.53,

        'salary_profiles' => [
            'Auxiliar de sistemas'   => 2_000_000,
            'Técnico de soporte'     => 3_000_000,
            'Ingeniero de sistemas'  => 4_500_000,
        ],

        'plans' => [
            'horas' => [
                'name'      => 'Por horas',
                'tagline'   => 'Respaldo puntual cuando lo necesites',
                'price'     => 120_000,
                'unit'      => 'hora',
                'monthly'   => 960_000, // referencia: bolsa mínima de 8 horas
                'features'  => [
                    'Soporte remoto y en sitio por demanda',
                    'Sin permanencia ni cargos fijos',
                    'Ciberseguridad básica incluida',
                    'Respuesta en horario laboral',
                ],
            ],
            'demanda' => [
                'name'      => 'Por demanda',
                'tagline'   => 'Bolsa mensual de horas con prioridad',
                'price'     => 1_500_000,
                'unit'      => 'mes',
                'monthly'   => 1_500_000,
                'features'  => [
                    'Bolsa mensual de horas con SLA prioritario',
                    'Mesa de ayuda y monitoreo de equipos',
                    'Ciberseguridad y copias de seguridad incluidas',
                    'Informe mensual de gestión',
                ],
            ],
            'dedicado' => [
                'name'      => 'Dedicado',
                'tagline'   => 'Un ingeniero asignado a tu operación',
                'price'     => 6_500_000,
                'unit'      => 'mes',
                'monthly'   => 6_500_000,
                'features'  => [
                    'Ingeniero asignado + equipo de respaldo',
                    'SLA de respuesta más exigente del portafolio',
                    'Ciberseguridad gestionada y auditorías periódicas',
                    'Consultoría y hoja de ruta tecnológica',
                ],
            ],
        ],
    ],

    'zone_surcharge' => [
        'Medellín - Centro'      => 0,
        'Medellín - El Poblado'  => 0,
        'Medellín - Laureles'    => 0,
        'Medellín - Belén'       => 0,
        'Medellín - Otro barrio' => 0,
        'Envigado'               => 0,
        'Itagüí'                 => 5,
        'Sabaneta'               => 8,
        'Bello'                  => 5,
        'La Estrella'            => 8,
        'Otro municipio'         => 12,
        'Medellín'               => 0,
        'El Poblado'             => 0,
        'Laureles'               => 0,
        'Caldas'                 => 12,
        'Copacabana'             => 10,
    ],
];
