<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Sistem Manajemen Anime Watchlist API",
    version: "1.0.0",
    description: "Dokumentasi REST API Proyek Akhir Web Service - Backend Service"
)]
#[OA\Server(
    url: "http://127.0.0.1:8001/api",
    description: "Main Backend Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class AppServiceProvider extends ServiceProvider
{
    /**
     * Anotasi Pancingan agar Swagger tidak error (Required @OA\PathItem() not found)
     */
    #[OA\Get(
        path: "/ping",
        summary: "Cek status server",
        responses: [
            new OA\Response(response: 200, description: "Server berjalan normal")
        ]
    )]
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