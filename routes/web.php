<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\DemographicAnalysisController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', [TestController::class, 'index']);

// Rutas de análisis demográfico
Route::prefix('demographic')->name('demographic.')->group(function () {
    Route::get('/', [DemographicAnalysisController::class, 'index'])->name('dashboard');
    Route::get('/comparar', [DemographicAnalysisController::class, 'comparar'])->name('comparar');
    Route::get('/provincia/{id}', [DemographicAnalysisController::class, 'provinciaDetalle'])->name('provincia-detalle');
    Route::get('/municipio/{id}', [DemographicAnalysisController::class, 'municipioDetalle'])->name('municipio-detalle');
});
