<?php

namespace App\Http\Controllers;

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
abstract class Controller
{
    //
}