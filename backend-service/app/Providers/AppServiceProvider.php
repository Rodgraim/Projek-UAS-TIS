<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Sistem Manajemen Anime Watchlist API",
    version: "1.0.0",
    description: "Dokumentasi lengkap REST API untuk Proyek Akhir Teknologi Integrasi Sistem. API ini menyediakan fitur autentikasi JWT, manajemen watchlist anime, pencarian anime via AniDB, dan manajemen user oleh admin.",
    contact: new OA\Contact(name: "Raka Paksi", email: "rakapaksi@example.com")
)]
#[OA\Server(
    url: "http://127.0.0.1:8001/api",
    description: "Backend Service Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan token JWT yang didapat dari endpoint /login"
)]
#[OA\Tag(name: "Authentication", description: "Endpoint untuk login, logout, dan profil user")]
#[OA\Tag(name: "Anime Search", description: "Endpoint pencarian anime dari AniDB external service")]
#[OA\Tag(name: "Watchlist", description: "Endpoint CRUD watchlist anime milik user")]
#[OA\Tag(name: "User Management", description: "Endpoint CRUD user (khusus admin)")]
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}