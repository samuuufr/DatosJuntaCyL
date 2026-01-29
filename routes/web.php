<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorAnalisisDemografico;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\MunicipioController;

Route::get('/', function () {
    return view('inicio');
});

// Rutas de análisis demográfico
Route::prefix('analisis-demografico')->name('analisis-demografico.')->group(function () {
    Route::get('/', [ControladorAnalisisDemografico::class, 'panel'])->name('panel');
    Route::get('/comparar', [ControladorAnalisisDemografico::class, 'comparar'])->name('comparar');
    Route::get('/provincia/{id}', [ControladorAnalisisDemografico::class, 'provinciaDetalle'])->name('provincia-detalle');
    Route::get('/municipio/{id}', [ControladorAnalisisDemografico::class, 'municipioDetalle'])->name('municipio-detalle');
    Route::get('/mapa-calor', [ControladorAnalisisDemografico::class, 'mapaCalor'])->name('mapa-calor');
});

// Rutas de provincias
Route::prefix('provincias')->name('provincias.')->group(function () {
    Route::get('/', [ProvinciaController::class, 'index'])->name('index');
});

// Rutas de municipios
Route::prefix('municipios')->name('municipios.')->group(function () {
    Route::get('/', [MunicipioController::class, 'index'])->name('index');
});

// API Routes para AJAX (sin middleware de autenticación para simplicidad)
Route::prefix('api')->name('api.')->group(function () {
    // API de provincias
    Route::get('/provincias/resumen', [ProvinciaController::class, 'resumen'])->name('provincias.resumen');
    Route::get('/provincias/{id}/datos', [ProvinciaController::class, 'datos'])->name('provincias.datos');

    // API de municipios
    Route::get('/municipios/buscar', [MunicipioController::class, 'buscar'])->name('municipios.buscar');
    Route::get('/municipios/{id}/datos', [MunicipioController::class, 'datos'])->name('municipios.datos');
    Route::get('/municipios/provincia/{provinciaId}', [MunicipioController::class, 'porProvincia'])->name('municipios.por-provincia');

    // API de mapa de calor
    Route::get('/mapa-calor/datos', [ControladorAnalisisDemografico::class, 'datosMapaCalor'])->name('mapa-calor.datos');
});
