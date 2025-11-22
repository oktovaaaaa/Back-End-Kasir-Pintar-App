<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'registerCashier']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [ApiAuthController::class, 'me'])->middleware('auth:sanctum');
});
