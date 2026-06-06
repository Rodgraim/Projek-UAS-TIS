<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AniDbService;
use OpenApi\Attributes as OA;

class AnimeController extends Controller
{
    protected $aniDbService;

    public function __construct(AniDbService $aniDbService)
    {
        $this->aniDbService = $aniDbService;
    }

    #[OA\Get(
        path: "/anime/search",
        summary: "Search Anime",
        description: "Mencari data anime dari external AniDB API berdasarkan keyword judul. Membutuhkan autentikasi JWT.",
        operationId: "searchAnime",
        tags: ["Anime Search"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "q",
                in: "query",
                required: true,
                description: "Keyword pencarian judul anime",
                schema: new OA\Schema(type: "string", example: "Naruto")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Data anime berhasil diambil dari AniDB",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Data anime dari AniDB berhasil diambil"),
                        new OA\Property(property: "data", type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "string", example: "20"),
                                    new OA\Property(property: "title", type: "string", example: "Naruto"),
                                    new OA\Property(property: "rating", type: "string", example: "8.25"),
                                    new OA\Property(property: "image", type: "string", example: "https://cdn.myanimelist.net/images/anime/13/17405.jpg")
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Query pencarian kosong",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Query pencarian tidak boleh kosong")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Query pencarian tidak boleh kosong'], 422);
        }

        $data = $this->aniDbService->searchAnime($query);
        return response()->json([
            'success' => true,
            'message' => 'Data anime dari AniDB berhasil diambil',
            'data' => $data
        ]);
    }
}