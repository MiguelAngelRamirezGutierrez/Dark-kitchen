<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

/*
| API REST del ERP (Clase 13). Todas las rutas quedan bajo /api/v1/...
| Versionar la API (v1) permite cambiarla en el futuro sin romper las apps que ya la usan.
*/
Route::prefix('v1')->group(function () {

    // Pública: máximo 5 intentos de login por minuto por IP
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Protegidas con token Bearer de Sanctum: 60 peticiones por minuto
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);

        Route::get('sales/{sale}', [SaleController::class, 'show']);
        Route::post('sales', [SaleController::class, 'store']);
    });
});
