<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorAnalisisDemografico;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;

Route::get('/', function () {
    return view('inicio');
})->name('inicio');

// Rutas de autenticación (públicas - solo para invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/registro', [AuthController::class, 'mostrarRegistro'])->name('registro');
    Route::post('/registro', [AuthController::class, 'registro'])->name('registro.post');
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rutas de perfil
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/', [PerfilController::class, 'mostrar'])->name('mostrar');
        Route::put('/actualizar', [PerfilController::class, 'actualizar'])->name('actualizar');
        Route::put('/actualizar-password', [PerfilController::class, 'actualizarPassword'])->name('actualizar-password');
        Route::get('/favoritos', [PerfilController::class, 'favoritos'])->name('favoritos');
    });
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

    // API de favoritos (protegida)
    Route::middleware(['auth', \App\Http\Middleware\EnsureJsonResponse::class])->prefix('perfil')->name('perfil.')->group(function () {
        Route::post('/favoritos/agregar', [PerfilController::class, 'agregarFavorito'])->name('favoritos.agregar');
        Route::post('/favoritos/eliminar', [PerfilController::class, 'eliminarFavorito'])->name('favoritos.eliminar');
        Route::get('/favoritos/{municipioId}/es-favorito', [PerfilController::class, 'esFavorito'])->name('favoritos.es-favorito');
        Route::get('/favoritos/lista', [PerfilController::class, 'listaFavoritosIds'])->name('favoritos.lista');
        Route::get('/favoritos/lista-completa', [PerfilController::class, 'listaFavoritosCompleta'])->name('favoritos.lista-completa');
    });
});
