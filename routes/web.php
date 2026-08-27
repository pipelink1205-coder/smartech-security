<?php

use App\Http\Controllers\Admin\GeocodeController;
use App\Http\Controllers\CollectionAccountController;
use App\Http\Controllers\ElectronicInvoiceController;
use App\Http\Controllers\EmployeeCardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WhatsappLeadController;
use Illuminate\Support\Facades\Route;

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

Route::get('/verificar-empleado/{employee:verification_token}', [EmployeeCardController::class, 'verify'])
    ->name('employees.verify');

// Cotizaciones
Route::post('/cotizar', [QuoteController::class, 'store'])->name('quotes.store');
Route::post('/whatsapp-lead', [WhatsappLeadController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('whatsapp-leads.store');
Route::get('/cotizacion/{quote}/pdf', [QuoteController::class, 'pdf'])
    ->name('quotes.pdf')
    ->middleware('signed');

Route::get('/factura/{invoice}/pdf', [ElectronicInvoiceController::class, 'pdf'])
    ->name('invoices.pdf')
    ->middleware('signed');

Route::get('/cuenta-de-cobro/{account}/pdf', [CollectionAccountController::class, 'pdf'])
    ->name('collection-accounts.pdf')
    ->middleware('signed');

// Páginas internas (para escalar)
Route::get('/servicios', [HomeController::class, 'servicios'])->name('servicios');
Route::get('/servicios/{service:slug}', [HomeController::class, 'servicioShow'])->name('servicios.show');
Route::get('/proyectos', [HomeController::class, 'proyectos'])->name('proyectos');
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
Route::view('/privacidad', 'pages.privacidad')->name('privacidad');
Route::view('/terminos-y-condiciones', 'pages.terminos')->name('terminos');
Route::view('/politica-de-ventas', 'pages.politica-ventas')->name('politica-ventas');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/geocode', GeocodeController::class)
        ->name('admin.geocode');

    Route::get('/admin/quotes/{quote}/pdf-preview', [QuoteController::class, 'preview'])
        ->name('admin.quotes.pdf-preview');

    Route::get('/admin/quotes/{quote}/pdf-download', [QuoteController::class, 'pdf'])
        ->name('admin.quotes.pdf-download');

    Route::get('/admin/invoices/{invoice}/pdf-preview', [ElectronicInvoiceController::class, 'preview'])
        ->name('admin.invoices.pdf-preview');

    Route::get('/admin/invoices/{invoice}/pdf-download', [ElectronicInvoiceController::class, 'pdf'])
        ->name('admin.invoices.pdf-download');

    Route::get('/admin/collection-accounts/{account}/pdf-preview', [CollectionAccountController::class, 'preview'])
        ->name('admin.collection-accounts.pdf-preview');

    Route::get('/admin/collection-accounts/{account}/pdf-download', [CollectionAccountController::class, 'pdf'])
        ->name('admin.collection-accounts.pdf-download');

    Route::get('/admin/employees/{employee}/card-preview', [EmployeeCardController::class, 'preview'])
        ->name('admin.employees.card-preview');

    Route::get('/admin/employees/{employee}/card-pdf', [EmployeeCardController::class, 'pdf'])
        ->name('admin.employees.card-pdf');

    Route::get('/admin/employees/{employee}/photo', [EmployeeCardController::class, 'photo'])
        ->name('admin.employees.photo');
});
