<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Supportf\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get All Users (Admin Only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Daftar user berhasil diambil")
     * )
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar user berhasil diambil',
            'data' => User::all()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create User (Admin Only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User berhasil dibuat"),
     *     @OA\Response(response=422, description="Validasi Gagal")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get User Detail (Admin Only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detail user ditemukan"),
     *     @OA\Response(response=404, description="User tidak ditemukan")
     * )
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail user ditemukan',
            'data' => $user
        ]);
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update User (Admin Only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User berhasil diubah"),
     *     @OA\Response(response=422, description="Validasi Gagal")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete User (Admin Only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User berhasil dihapus"),
     *     @OA\Response(response=404, description="User tidak ditemukan")
     * )
     */
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