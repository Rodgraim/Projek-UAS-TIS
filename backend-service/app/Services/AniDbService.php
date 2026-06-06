<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class AniDbService
{
    public function searchAnime(string $query)
    {
        $response = Http::get("https://api.jikan.moe/v4/anime", [
            'q' => $query,
            'limit' => 8
        ]);

        if ($response->failed()) return [];

        $results = [];
        foreach ($response->json()['data'] ?? [] as $item) {
            $results[] = [
                'id' => (string)$item['mal_id'],
                'title' => $item['title'],
                'rating' => (string)($item['score'] ?? '0'),
                'image' => $item['images']['jpg']['image_url'] ?? '',
            ];
        }
        return $results;
    }
}