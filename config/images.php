<?php

$u = static fn (string $id, int $w = 800) =>
    "https://images.unsplash.com/{$id}?ixlib=rb-4.0.3&auto=format&fit=crop&w={$w}&q=80";

$local = static function (string $path, string $fallback) {
    return file_exists(public_path($path)) ? $path : $fallback;
};

return [
    'hero' => $local('images/hero-cover.png', $local('images/hero.jpg', $u('photo-1558002038-1055907df827', 1200))),

    'iptv' => [
        'primary'   => $local('images/iptv-primary.png', $local('images/iptv-primary.jpg', $u('photo-1582719508461-905c673771fd'))),
        'secondary' => $local('images/iptv-secondary.png', $local('images/iptv-secondary.jpg', $u('photo-1566073771259-6a8506099945'))),
    ],

    'services' => [
        'camaras-4k'     => $local('images/services/camaras-4k.png', $local('images/services/camaras.jpg', $u('photo-1581091226033-d5c48150dbaa'))),
        'energia-solar'  => $local('images/services/solar.jpg', $u('photo-1509391366360-2e959784a276')),
        'control-acceso' => $local('images/services/acceso.jpg', $u('photo-1573164713714-d95e436ab8d6')),
        'alarmas'        => $local('images/services/alarmas.png', $local('images/services/alarmas.jpg', $u('photo-1563986768609-322da13575f3'))),
        'domotica'       => $local('images/services/domotica.png', $local('images/services/domotica.jpg', $u('photo-1558002038-1055907df827'))),
        'redes-fibra'    => $local('images/services/redes.jpg', $u('photo-1558494949-ef010cbdcc31')),
    ],

    'projects' => [
        'edificio-torre-ejecutiva' => $local('images/projects/torre-ejecutiva.jpg', $u('photo-1621905251189-08b45d6a269e', 600)),
        'hotel-boutique'             => $local('images/projects/hotel-boutique.jpg', $u('photo-1566073771259-6a8506099945', 600)),
        'conjunto-altos-envigado'    => $local('images/projects/solar-envigado.jpg', $u('photo-1509391366360-2e959784a276', 600)),
        'centro-comercial-mayorca'   => $local('images/projects/mayorca.jpg', $u('photo-1573164713714-d95e436ab8d6', 600)),
        'textiles-medellin'          => $local('images/projects/redes-bello.jpg', $u('photo-1558494949-ef010cbdcc31', 600)),
        'apartamento-laureles'       => $local('images/projects/domotica-laureles.jpg', $u('photo-1582719478250-c89cae4dc85b', 600)),
    ],

    'testimonials' => [
        'https://randomuser.me/api/portraits/women/44.jpg',
        'https://randomuser.me/api/portraits/men/32.jpg',
        'https://randomuser.me/api/portraits/men/85.jpg',
        'https://randomuser.me/api/portraits/men/75.jpg',
        'https://randomuser.me/api/portraits/women/68.jpg',
        'https://randomuser.me/api/portraits/men/52.jpg',
    ],
];
