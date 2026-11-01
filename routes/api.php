<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TreeCareController;
use App\Http\Controllers\TreeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn (Request $request) => $request->user());

    Route::get('/trees', [TreeController::class, 'index']);
    Route::post('/trees', [TreeController::class, 'store']);
    Route::get('/trees/{tree}', [TreeController::class, 'show']);
    Route::post('/trees/{tree}/care', TreeCareController::class);
});
