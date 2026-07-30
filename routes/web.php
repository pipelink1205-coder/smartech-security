<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GeocodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SitemapController;

// GeoJSON del mapa de comunas (sirve el archivo con headers correctos en producción)
Route::get('/mapa/comunas.geojson', function () {
    $path = public_path('data/comunas-medellin.geojson');

    abort_unless(is_readable($path), 404);

    return response()->file($path, [
        'Content-Type' => 'application/geo+json; charset=utf-8',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->name('map.comunas-geojson');

// Sitemap para Google Search Console
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

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

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/geocode', GeocodeController::class)
        ->name('admin.geocode');

    Route::get('/admin/quotes/{quote}/pdf-preview', [QuoteController::class, 'preview'])
        ->name('admin.quotes.pdf-preview');

    Route::get('/admin/quotes/{quote}/pdf-download', [QuoteController::class, 'pdf'])
        ->name('admin.quotes.pdf-download');
});
