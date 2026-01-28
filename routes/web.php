<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ControladorAnalisisDemografico;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [TestController::class, 'index']);

// Rutas de análisis demográfico
Route::prefix('analisis-demografico')->name('analisis-demografico.')->group(function () {
    Route::get('/', [ControladorAnalisisDemografico::class, 'panel'])->name('panel');
    Route::get('/comparar', [ControladorAnalisisDemografico::class, 'comparar'])->name('comparar');
    Route::get('/provincia/{id}', [ControladorAnalisisDemografico::class, 'provinciaDetalle'])->name('provincia-detalle');
    Route::get('/municipio/{id}', [ControladorAnalisisDemografico::class, 'municipioDetalle'])->name('municipio-detalle');
});
