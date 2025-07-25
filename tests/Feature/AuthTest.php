<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'admin122@admin.com',
            'password' => bcrypt('asdasdasd1'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin122@admin.com',
            'password' => 'asdasdasd1',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'token_type',
        ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'wrong@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }
}
