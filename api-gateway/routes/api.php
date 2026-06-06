<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

Route::any('{any}', function (Request $request, $any) {
    $backendUrl = 'http://127.0.0.1:8001/api/' . $any;

    $headers = $request->headers->all();
    $formattedHeaders = [];
    foreach ($headers as $key => $value) {
        $formattedHeaders[$key] = $value[0];
    }

    $contentType = $request->header('Content-Type');
    $pendingRequest = Http::withHeaders($formattedHeaders);

    if ($contentType !== null) {
        $pendingRequest = $pendingRequest->withBody($request->getContent(), $contentType);
    }

    $response = $pendingRequest->send($request->method(), $backendUrl, [
        'query' => $request->query()
    ]);

    $body = $response->body();
    $data = json_decode($body, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        return response()->json($data, $response->status());
    }

    return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan pada komunikasi Backend Service.',
        'errors' => $body
    ], $response->status());
})->where('any', '.*');