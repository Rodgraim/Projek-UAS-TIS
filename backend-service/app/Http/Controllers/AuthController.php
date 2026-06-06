<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/login",
        summary: "Login User",
        description: "Autentikasi user dengan email dan password, mengembalikan token JWT untuk akses API.",
        operationId: "loginUser",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Kredensial login",
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Login berhasil"),
                        new OA\Property(property: "data", type: "object",
                            properties: [
                                new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
                                new OA\Property(property: "user", type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer", example: 1),
                                        new OA\Property(property: "name", type: "string", example: "Admin"),
                                        new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                                        new OA\Property(property: "role", type: "string", example: "admin")
                                    ]
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Email atau password salah",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Email atau password salah"),
                        new OA\Property(property: "errors", type: "string", nullable: true, example: null)
                    ]
                )
            )
        ]
    )]
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

    #[OA\Get(
        path: "/profile",
        summary: "Get User Profile",
        description: "Mengambil data profil user yang sedang login berdasarkan token JWT.",
        operationId: "getUserProfile",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Profile berhasil diambil"),
                        new OA\Property(property: "data", type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Admin"),
                                new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                                new OA\Property(property: "role", type: "string", example: "admin"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
    public function profile()
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diambil',
            'data' => auth()->user()
        ]);
    }

    #[OA\Post(
        path: "/logout",
        summary: "Logout User",
        description: "Menginvalidasi token JWT user yang sedang login.",
        operationId: "logoutUser",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout berhasil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Logout berhasil"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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