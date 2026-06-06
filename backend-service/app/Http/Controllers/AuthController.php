<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
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

    public function profile()
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diambil',
            'data' => auth()->user()
        ]);
    }

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