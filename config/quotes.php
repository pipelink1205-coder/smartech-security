<?php

return [
    'company' => [
        'legal_name' => env('QUOTE_COMPANY_NAME', 'SMART TECH SECURITY S.A.S.'),
        'tax_id' => env('QUOTE_COMPANY_NIT'),
        'address' => env('QUOTE_COMPANY_ADDRESS', config('contact.address')),
        'city' => env('QUOTE_COMPANY_CITY', 'Envigado, Antioquia'),
        'website' => env('QUOTE_COMPANY_WEBSITE', 'smarttechsecurity.com.co'),
    ],

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
        'Ciberseguridad Empresarial'   => [1_200_000, 8_000_000],
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
                'name'      => 'Soporte TI Especializado',
                'tagline'   => 'Atención por evento o mediante bolsa de horas',
                'price'     => 100_000,
                'unit'      => 'hora',
                'monthly'   => 380_000, // referencia: bolsa Inicial de 4 horas
                'features'  => [
                    'Incidentes, diagnósticos y configuraciones puntuales',
                    'Soporte remoto y visitas según necesidad',
                    'Bolsas desde 4 horas con tarifa preferencial',
                    'Repuestos, licencias y proyectos se cotizan aparte',
                ],
            ],
            'demanda' => [
                'name'      => 'Outsourcing TI Administrado',
                'tagline'   => 'Operación TI administrada, preventiva y medible',
                'price'     => 650_000,
                'unit'      => 'mes',
                'monthly'   => 650_000,
                'features'  => [
                    'Mesa de ayuda y gestión de tickets',
                    'Soporte Nivel 1 y Nivel 2 según el plan',
                    'Administración y actividades preventivas según alcance',
                    'Informe periódico y recomendaciones de mejora',
                ],
            ],
            'dedicado' => [
                'name'      => 'Profesional TI Dedicado',
                'tagline'   => 'Un profesional asignado con respaldo de Smart Tech',
                'price'     => 3_500_000,
                'unit'      => 'mes',
                'monthly'   => 3_500_000,
                'features'  => [
                    'Técnico, analista o ingeniero según el perfil requerido',
                    'Dedicación parcial; tiempo completo desde $6.500.000',
                    'Gestión diaria, inventario y documentación',
                    'Escalamiento a especialistas de Smart Tech',
                ],
            ],
        ],
    ],

    /*
     * Condiciones por defecto de la cotización formal (PDF / Word estilo).
     */
    'default_terms' => "Validez: 15 días calendario.\n"
        ."Precios en pesos colombianos (COP).\n"
        ."El IVA se discrimina por concepto cuando corresponda.\n"
        ."La instalación se agenda tras aprobación escrita o por WhatsApp.\n"
        ."Garantía de 1 año en mano de obra; equipos según garantía de fábrica.",

    'default_payment_terms' => "50% de anticipo para iniciar.\n"
        ."50% contra entrega, instalación o puesta en funcionamiento.",

    'default_warranty_terms' => "Mano de obra: 12 meses desde la entrega.\n"
        ."Equipos: según las condiciones y el tiempo de garantía del fabricante.",

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
