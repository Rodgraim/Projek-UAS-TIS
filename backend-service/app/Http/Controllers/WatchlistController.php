<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Watchlist;

class WatchlistController extends Controller
{
    /**
     * @OA\Get(
     *     path="/watchlists",
     *     summary="Get All Watchlists",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar watchlist diambil")
     * )
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $watchlist = Watchlist::with('user')->get();
        } else {
            $watchlist = Watchlist::where('user_id', $user->id)->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar watchlist berhasil diambil',
            'data' => $watchlist
        ]);
    }

    /**
     * @OA\Post(
     *     path="/watchlists",
     *     summary="Add to Watchlist",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="anime_id", type="string"),
     *             @OA\Property(property="anime_title", type="string"),
     *             @OA\Property(property="anime_rating", type="string"),
     *             @OA\Property(property="anime_image", type="string"),
     *             @OA\Property(property="status_watching", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Anime ditambahkan ke watchlist"),
     *     @OA\Response(response=422, description="Validasi Gagal")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'anime_id' => 'required',
            'anime_title' => 'required'
        ]);

        $watchlist = Watchlist::create([
            'user_id' => auth()->id(),
            'anime_id' => $request->anime_id,
            'anime_title' => $request->anime_title,
            'anime_rating' => $request->anime_rating,
            'anime_image' => $request->anime_image,
            'status_watching' => $request->status_watching ?? 'Plan to Watch'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anime berhasil ditambahkan ke watchlist',
            'data' => $watchlist
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/watchlists/{id}",
     *     summary="Get Watchlist Detail",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detail watchlist ditemukan"),
     *     @OA\Response(response=403, description="Akses dilarang")
     * )
     */
    public function show($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        if (auth()->user()->role !== 'admin' && $watchlist->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses dilarang'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail watchlist ditemukan',
            'data' => $watchlist
        ]);
    }

    /**
     * @OA\Put(
     *     path="/watchlists/{id}",
     *     summary="Update Watchlist Status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status_watching", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status watchlist diperbarui"),
     *     @OA\Response(response=403, description="Akses dilarang")
     * )
     */
    public function update(Request $request, $id)
    {
        $watchlist = Watchlist::findOrFail($id);
        if (auth()->user()->role !== 'admin' && $watchlist->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses dilarang'], 403);
        }

        $watchlist->update($request->only('status_watching'));

        return response()->json([
            'success' => true,
            'message' => 'Status watchlist diperbarui',
            'data' => $watchlist
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/watchlists/{id}",
     *     summary="Delete Watchlist",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Watchlist dihapus"),
     *     @OA\Response(response=403, description="Akses dilarang")
     * )
     */
    public function destroy($id)
    {
        $watchlist = Watchlist::findOrFail($id);
        if (auth()->user()->role !== 'admin' && $watchlist->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses dilarang'], 403);
        }

        $watchlist->delete();
        return response()->json([
            'success' => true,
            'message' => 'Watchlist berhasil dihapus',
            'data' => null
        ]);
    }
}