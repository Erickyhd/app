<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Modules\Jerarquias\Infrastructure\Http\Controllers\JerarquiaController;
use App\Modules\Rangos\Infrastructure\Http\Controllers\RangoController;
use App\Modules\Trabajadores\Infrastructure\Http\Controllers\TrabajadorController;
use App\Modules\Clientes\Infrastructure\Http\Controllers\ClienteController;
use App\Modules\Users\Infrastructure\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    
    // USUARIOS
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
    
    // JERARQUIAS
    Route::prefix('jerarquias')->group(function () {
        Route::get('/', [JerarquiaController::class, 'index']);
        Route::post('/', [JerarquiaController::class, 'store']);
        Route::get('/{id}', [JerarquiaController::class, 'show']);
        Route::put('/{id}', [JerarquiaController::class, 'update']);
        Route::delete('/{id}', [JerarquiaController::class, 'destroy']);
    });

    // RANGOS
    Route::prefix('rangos')->group(function () {
        Route::get('/', [RangoController::class, 'index']);
        Route::post('/', [RangoController::class, 'store']);
        Route::get('/{id}', [RangoController::class, 'show']);
        Route::put('/{id}', [RangoController::class, 'update']);
        Route::delete('/{id}', [RangoController::class, 'destroy']);
    });

    // TRABAJADORES
    Route::prefix('trabajadores')->group(function () {
        Route::get('/', [TrabajadorController::class, 'index']);
        Route::post('/', [TrabajadorController::class, 'store']);
        Route::get('/{id}', [TrabajadorController::class, 'show']);
        Route::put('/{id}', [TrabajadorController::class, 'update']);
        Route::delete('/{id}', [TrabajadorController::class, 'destroy']);
    });

    // CLIENTES
    Route::prefix('clientes')->group(function () {
        Route::get('/', [ClienteController::class, 'index']);
        Route::post('/', [ClienteController::class, 'store']);
        Route::get('/{id}', [ClienteController::class, 'show']);
        Route::put('/{id}', [ClienteController::class, 'update']);
        Route::delete('/{id}', [ClienteController::class, 'destroy']);
    });

});
