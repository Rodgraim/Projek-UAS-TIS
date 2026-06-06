<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login User",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login Berhasil"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
                'errors' => null
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => auth('api')->user()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/profile",
     *     summary="Get User Profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Profile diambil"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function profile()
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diambil',
            'data' => auth()->user()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout User",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout berhasil"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
            'data' => null
        ]);
    }
}