<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

Route::get('/', function () {
    return response()->json(['status' => 'OK']);
});

$backendUrl = 'http://127.0.0.1:8001/api';

Route::any('/gateway/{any}', function (Request $request, $any) use ($backendUrl) {
    $method = $request->method();
    $url = "{$backendUrl}/{$any}";
    
    $token = $request->bearerToken();
    $client = Http::withHeaders($token ? ['Authorization' => "Bearer {$token}"] : []);

    $response = $client->send($method, $url, [
        'json' => $request->json()->all(),
        'query' => $request->query()
    ]);

    return response()->json($response->json(), $response->status());
})->where('any', '.*');