<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GeocodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuoteController;

// GeoJSON del mapa de comunas (sirve el archivo con headers correctos en producción)
Route::get('/mapa/comunas.geojson', function () {
    $path = public_path('data/comunas-medellin.geojson');

    abort_unless(is_readable($path), 404);

    return response()->file($path, [
        'Content-Type' => 'application/geo+json; charset=utf-8',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('map.comunas-geojson');

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Cotizaciones
Route::post('/cotizar', [QuoteController::class, 'store'])->name('quotes.store');
Route::get('/cotizacion/{quote}/pdf', [QuoteController::class, 'pdf'])
    ->name('quotes.pdf')
    ->middleware('signed');

// Páginas internas (para escalar)
Route::get('/servicios', [HomeController::class, 'servicios'])->name('servicios');
Route::get('/servicios/{service:slug}', [HomeController::class, 'servicioShow'])->name('servicios.show');
Route::get('/proyectos', [HomeController::class, 'proyectos'])->name('proyectos');
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
Route::view('/privacidad', 'pages.privacidad')->name('privacidad');

// Geocodificación para el mapa del panel admin (requiere sesión)
Route::middleware(['web', 'auth'])
    ->get('/admin/geocode', GeocodeController::class)
    ->name('admin.geocode');
