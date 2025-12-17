<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeanController;
use App\Http\Controllers\GrinderController;
use App\Http\Controllers\CalibrationSessionController;
use App\Http\Controllers\ShotController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Beans
    Route::apiResource('beans', BeanController::class);

    // Grinders
    Route::apiResource('grinders', GrinderController::class);

    // Calibration Sessions
    Route::apiResource('calibration-sessions', CalibrationSessionController::class);
    Route::get('/beans/{bean}/sessions', [CalibrationSessionController::class, 'index']);

    // Shots
    Route::prefix('calibration-sessions/{session}')->group(function () {
        Route::apiResource('shots', ShotController::class)->except(['index', 'show']);
        Route::get('/shots', [ShotController::class, 'index']);
        Route::get('/shots/{shot}', [ShotController::class, 'show']);
    });
});
