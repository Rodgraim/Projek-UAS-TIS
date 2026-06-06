<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Watchlist;
use OpenApi\Attributes as OA;

class WatchlistController extends Controller
{
    #[OA\Get(
        path: "/watchlists",
        summary: "Get All Watchlists",
        description: "Mengambil semua data watchlist milik user yang login. Admin dapat melihat seluruh watchlist dari semua user.",
        operationId: "getWatchlists",
        tags: ["Watchlist"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Daftar watchlist berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Daftar watchlist berhasil diambil"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "user_id", type: "integer", example: 1),
                                    new OA\Property(property: "anime_id", type: "string", example: "20"),
                                    new OA\Property(property: "anime_title", type: "string", example: "Naruto"),
                                    new OA\Property(property: "anime_rating", type: "string", example: "8.25"),
                                    new OA\Property(property: "anime_image", type: "string", example: "https://cdn.myanimelist.net/images/anime/13/17405.jpg"),
                                    new OA\Property(property: "status_watching", type: "string", example: "Watching"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time")
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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

    #[OA\Post(
        path: "/watchlists",
        summary: "Add to Watchlist",
        description: "Menambahkan anime ke watchlist user yang sedang login.",
        operationId: "addToWatchlist",
        tags: ["Watchlist"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Data anime yang akan ditambahkan ke watchlist",
            content: new OA\JsonContent(
                required: ["anime_id", "anime_title"],
                properties: [
                    new OA\Property(property: "anime_id", type: "string", example: "20", description: "ID anime dari AniDB"),
                    new OA\Property(property: "anime_title", type: "string", example: "Naruto", description: "Judul anime"),
                    new OA\Property(property: "anime_rating", type: "string", example: "8.25", description: "Rating anime"),
                    new OA\Property(property: "anime_image", type: "string", example: "https://cdn.myanimelist.net/images/anime/13/17405.jpg", description: "URL gambar anime"),
                    new OA\Property(property: "status_watching", type: "string", example: "Plan to Watch", description: "Status: Plan to Watch, Watching, Completed")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Anime berhasil ditambahkan ke watchlist",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Anime berhasil ditambahkan ke watchlist"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "user_id", type: "integer", example: 1),
                                new OA\Property(property: "anime_id", type: "string", example: "20"),
                                new OA\Property(property: "anime_title", type: "string", example: "Naruto"),
                                new OA\Property(property: "anime_rating", type: "string", example: "8.25"),
                                new OA\Property(property: "status_watching", type: "string", example: "Plan to Watch"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validasi gagal",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "The anime_id field is required."),
                        new OA\Property(property: "errors", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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

    #[OA\Get(
        path: "/watchlists/{id}",
        summary: "Get Watchlist Detail",
        description: "Mengambil detail satu item watchlist berdasarkan ID.",
        operationId: "getWatchlistDetail",
        tags: ["Watchlist"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID watchlist", schema: new OA\Schema(type: "integer", example: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detail watchlist ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Detail watchlist ditemukan"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "user_id", type: "integer", example: 1),
                                new OA\Property(property: "anime_id", type: "string", example: "20"),
                                new OA\Property(property: "anime_title", type: "string", example: "Naruto"),
                                new OA\Property(property: "anime_rating", type: "string", example: "8.25"),
                                new OA\Property(property: "status_watching", type: "string", example: "Watching"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses dilarang (bukan pemilik watchlist)"),
            new OA\Response(response: 404, description: "Watchlist tidak ditemukan"),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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

    #[OA\Put(
        path: "/watchlists/{id}",
        summary: "Update Watchlist Status",
        description: "Memperbarui status tontonan: Plan to Watch → Watching → Completed.",
        operationId: "updateWatchlistStatus",
        tags: ["Watchlist"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID watchlist", schema: new OA\Schema(type: "integer", example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Status tontonan baru",
            content: new OA\JsonContent(
                required: ["status_watching"],
                properties: [
                    new OA\Property(property: "status_watching", type: "string", example: "Watching", description: "Status: Plan to Watch, Watching, atau Completed")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Status watchlist berhasil diperbarui",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Status watchlist diperbarui"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "anime_title", type: "string", example: "Naruto"),
                                new OA\Property(property: "status_watching", type: "string", example: "Watching")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses dilarang"),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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

    #[OA\Delete(
        path: "/watchlists/{id}",
        summary: "Delete Watchlist",
        description: "Menghapus item watchlist berdasarkan ID.",
        operationId: "deleteWatchlist",
        tags: ["Watchlist"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID watchlist", schema: new OA\Schema(type: "integer", example: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Watchlist berhasil dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Watchlist berhasil dihapus"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            ),
            new OA\Response(response: 403, description: "Akses dilarang"),
            new OA\Response(response: 404, description: "Watchlist tidak ditemukan"),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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