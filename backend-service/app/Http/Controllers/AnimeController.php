<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AniDbService;

class AnimeController extends Controller
{
    protected $aniDbService;

    public function __construct(AniDbService $aniDbService)
    {
        $this->aniDbService = $aniDbService;
    }

    /**
     * @OA\Get(
     *     path="/anime/search",
     *     summary="Search Anime on AniDB",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="q", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Data anime berhasil diambil"),
     *     @OA\Response(response=422, description="Query kosong")
     * )
     */
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