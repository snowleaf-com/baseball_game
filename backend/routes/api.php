<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 認証
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // 認証が必要なルート
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // 試合
        Route::get('/games', [GameController::class, 'index']);
        Route::post('/games/play', [GameController::class, 'play']);
        Route::get('/games/{game}', [GameController::class, 'show']);
    });
});

