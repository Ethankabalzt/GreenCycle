<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TreeController;
use App\Models\SeedType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas públicas de Autenticación
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Tipos de semilla disponibles (público: el catálogo se puede consultar sin sesión)
Route::get('/seed-types', function () {
    return SeedType::orderBy('name')->get();
});

// Rutas protegidas de la aplicación
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/trees', [TreeController::class, 'index']);
    Route::get('/trees/{tree}', [TreeController::class, 'show']);
    Route::post('/trees', [TreeController::class, 'store']);
});
