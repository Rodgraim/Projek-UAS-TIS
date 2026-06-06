<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: "/users",
        summary: "Get All Users",
        description: "Mengambil daftar seluruh user yang terdaftar. Endpoint ini hanya bisa diakses oleh admin.",
        operationId: "getAllUsers",
        tags: ["User Management"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Daftar user berhasil diambil",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Daftar user berhasil diambil"),
                        new OA\Property(property: "data", type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Admin"),
                                    new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                                    new OA\Property(property: "role", type: "string", example: "admin"),
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
        return response()->json([
            'success' => true,
            'message' => 'Daftar user berhasil diambil',
            'data' => User::all()
        ]);
    }

    #[OA\Post(
        path: "/users",
        summary: "Create User",
        description: "Membuat user baru. Endpoint ini hanya bisa diakses oleh admin.",
        operationId: "createUser",
        tags: ["User Management"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Data user baru",
            content: new OA\JsonContent(
                required: ["name", "email", "password", "role"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "role", type: "string", example: "user", description: "Role: admin, manager, atau user")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "User berhasil dibuat"),
                        new OA\Property(property: "data", type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 4),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "email", type: "string", example: "john@example.com"),
                                new OA\Property(property: "role", type: "string", example: "user"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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

    #[OA\Get(
        path: "/users/{id}",
        summary: "Get User Detail",
        description: "Mengambil detail satu user berdasarkan ID. Hanya admin.",
        operationId: "getUserDetail",
        tags: ["User Management"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID user", schema: new OA\Schema(type: "integer", example: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detail user ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Detail user ditemukan"),
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
            new OA\Response(response: 404, description: "User tidak ditemukan"),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail user ditemukan',
            'data' => $user
        ]);
    }

    #[OA\Put(
        path: "/users/{id}",
        summary: "Update User",
        description: "Memperbarui data user berdasarkan ID. Hanya admin. Password bersifat opsional.",
        operationId: "updateUser",
        tags: ["User Management"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID user", schema: new OA\Schema(type: "integer", example: 1))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Data user yang akan diperbarui",
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Admin Updated"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "newpassword123", description: "Opsional"),
                    new OA\Property(property: "role", type: "string", example: "admin")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User berhasil diubah",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "User berhasil diubah"),
                        new OA\Property(property: "data", type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Admin Updated"),
                                new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                                new OA\Property(property: "role", type: "string", example: "admin")
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "User tidak ditemukan"),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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

    #[OA\Delete(
        path: "/users/{id}",
        summary: "Delete User",
        description: "Menghapus user berdasarkan ID. Hanya admin.",
        operationId: "deleteUser",
        tags: ["User Management"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID user", schema: new OA\Schema(type: "integer", example: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User berhasil dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "User berhasil dihapus"),
                        new OA\Property(property: "data", type: "string", nullable: true, example: null)
                    ]
                )
            ),
            new OA\Response(response: 404, description: "User tidak ditemukan"),
            new OA\Response(response: 401, description: "Token tidak valid atau expired")
        ]
    )]
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