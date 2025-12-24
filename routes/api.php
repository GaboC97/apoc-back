<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReintegroController;
use App\Http\Controllers\SubsidioController;
use App\Http\Controllers\OrganismoController;
use App\Http\Controllers\RazonGenericaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactoController; 
// ---------------------
// LOGIN (PÚBLICO)
// ---------------------
Route::post('/login', [AuthController::class, 'login']);


// ---------------------
// 2. RUTA DE CONTACTO 
// ---------------------
Route::post('/contacto/send', [ContactoController::class, 'send']);

// ---------------------
// RUTAS PROTEGIDAS
// ---------------------
Route::middleware('auth:sanctum')->group(function () {

    // Usuario actual
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::get('/dashboard-stats', [DashboardController::class, 'stats']);

    // ---------------------
    // REINTEGROS USER
    // ---------------------
    Route::get('/reintegros', [ReintegroController::class, 'index']);
    Route::post('/reintegros', [ReintegroController::class, 'store']);
    Route::get('/reintegros/{id}', [ReintegroController::class, 'show']);
    // ---------------------
    // SUBSIDIOS USER
    // ---------------------
    Route::get('/subsidios', [SubsidioController::class, 'index']);
    Route::post('/subsidios', [SubsidioController::class, 'store']);
    Route::get('/subsidios/{id}', [SubsidioController::class, 'show']);
    // ---------------------
    // ORGANISMOS
    // --------------------
    Route::get('/organismos', [OrganismoController::class, 'index']);


    // ---------------------
    // RAZONES GENÉRICAS
    // ---------------------
    Route::get('/razones/reintegro', [RazonGenericaController::class, 'reintegros']);
    Route::get('/razones/subsidio', [RazonGenericaController::class, 'subsidios']);

    // ---------------------
    // ADMIN
    // ---------------------
    Route::middleware('admin')->prefix('admin')->group(function () {

        // LISTA DE USUARIOS
        Route::get('/usuarios', function () {
            return \App\Models\User::select('id', 'name', 'dni', 'cbu_alias', 'email', 'telefono') // Agregué cbu_alias por si acaso
                ->orderBy('name')
                ->get();
        });
    
        // REINTEGROS (ADMIN)
        Route::get('/reintegros', [ReintegroController::class, 'all']);
        Route::post('/reintegros/{id}/aprobar', [ReintegroController::class, 'aprobar']);
        Route::post('/reintegros/{id}/rechazar', [ReintegroController::class, 'rechazar']);
        Route::get('/reintegros/historial', [ReintegroController::class, 'historial']);
        

        // SUBSIDIOS (ADMIN)
        Route::get('/subsidios', [SubsidioController::class, 'all']);
        Route::post('/subsidios/{id}/aprobar', [SubsidioController::class, 'aprobar']);
        Route::post('/subsidios/{id}/rechazar', [SubsidioController::class, 'rechazar']);
        Route::get('/subsidios/historial', [SubsidioController::class, 'historial']);
    });
});