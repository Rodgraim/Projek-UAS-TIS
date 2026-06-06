<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\AnimeController;

/**
 * @OA\Info(
 * title="Sistem Manajemen Anime Watchlist API",
 * version="1.0.0",
 * description="Dokumentasi REST API Proyek Akhir Web Service - Backend Service",
 * @OA\Contact(
 * email="rakapaksi@example.com"
 * )
 * )
 * * @OA\Server(
 * url="http://127.0.0.1:8001/api",
 * description="Main Backend Server"
 * )
 * * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT"
 * )
 */

// Public Route
Route::post('/login', [AuthController::class, 'login']);

// Protected Route (Harus Login JWT via auth:api)
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Watchlist & Anime Search (Akses untuk Admin & User)
    Route::apiResource('watchlists', WatchlistController::class);
    Route::get('/anime/search', [AnimeController::class, 'search']);

    // Khusus Admin saja
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});