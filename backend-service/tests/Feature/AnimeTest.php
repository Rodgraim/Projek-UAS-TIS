<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_search_anime(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Mock external Jikan API response
        Http::fake([
            'https://api.jikan.moe/v4/anime*' => Http::response([
                'data' => [
                    [
                        'mal_id' => 1,
                        'title' => 'Naruto',
                        'score' => 8.2,
                        'images' => [
                            'jpg' => [
                                'image_url' => 'https://example.com/naruto.jpg'
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/anime/search?q=Naruto');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Data anime dari AniDB berhasil diambil',
                'data' => [
                    [
                        'id' => '1',
                        'title' => 'Naruto',
                        'rating' => '8.2',
                        'image' => 'https://example.com/naruto.jpg',
                    ]
                ]
            ]);
    }
}
