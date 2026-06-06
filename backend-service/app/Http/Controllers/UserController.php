<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Supportf\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar user berhasil diambil',
            'data' => User::all()
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data' => $user
        ], 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail user ditemukan',
            'data' => $user
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diubah',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus',
            'data' => null
        ]);
    }
}