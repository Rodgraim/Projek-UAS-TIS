<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Watchlist;

class WatchlistController extends Controller
{
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