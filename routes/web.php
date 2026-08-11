<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/logout', [AuthController::class, 'logout']);
Route::get('/api/user', [AuthController::class, 'user'])->middleware('auth');

use App\Http\Controllers\UserController;
Route::apiResource('/api/users', UserController::class);

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

