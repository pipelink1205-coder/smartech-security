<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuoteController;

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Cotizaciones
Route::post('/cotizar', [QuoteController::class, 'store'])->name('quotes.store');
Route::get('/cotizacion/{quote}/pdf', [QuoteController::class, 'pdf'])
    ->name('quotes.pdf')
    ->middleware('signed');

// Páginas internas (para escalar)
Route::get('/servicios', [HomeController::class, 'servicios'])->name('servicios');
Route::get('/proyectos', [HomeController::class, 'proyectos'])->name('proyectos');
Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
