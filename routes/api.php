<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticuloController;

/*
|--------------------------------------------------------------------------
| Rutas de la API (Autenticación y Roles)
|--------------------------------------------------------------------------
*/

// Rutas Públicas (Registro y Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas de Artículos Públicas (index y show)
Route::resource('articulos', ArticuloController::class)->only([
    'index',
    'show'
]);

// Rutas Protegidas (Requieren Sanctum Token Y Roles)
Route::middleware('auth:sanctum')->group(function () {
    // Ruta para obtener el usuario actual
    Route::get('/user', [AuthController::class, 'show']);
    // Ruta de Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // RUTAS CRUD DE ARTÍCULOS PROTEGIDAS

    // 1. Crear Artículo (POST): Solo para 'administrador' o 'editor'
    Route::post('/articulos', [ArticuloController::class, 'store'])
        ->middleware('role:administrador,editor');

    // 2. Editar Artículo (PUT): Solo para 'administrador' o 'editor'
    Route::put('/articulos/{articulo}', [ArticuloController::class, 'update'])
        ->middleware('role:administrador,editor');

    // 3. Eliminar Artículo (DELETE): Solo para 'administrador'
    Route::delete('/articulos/{articulo}', [ArticuloController::class, 'destroy'])
        ->middleware('role:administrador');

    // 4. CLAVE: RUTA DE ADMINISTRACIÓN PARA EL TEST
    Route::get('/admin-route', [AuthController::class, 'checkAdmin'])
        ->middleware('role:administrador');
});
