<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create customer role
        Role::create(['name' => 'customer', 'description' => 'Customer']);
    }

    /** @test */
    public function it_can_authenticate_user_via_social_login()
    {
        $response = $this->postJson('/api/v1/auth/social/login', [
            'provider' => 'google',
            'provider_id' => '123456789',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'provider',
                    'provider_id',
                    'avatar',
                    'created_at',
                    'updated_at',
                ],
                'token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'provider' => 'google',
            'provider_id' => '123456789',
        ]);
    }

    /** @test */
    public function it_can_link_existing_user_to_social_provider()
    {
        // Create a user first
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/social/login', [
            'provider' => 'google',
            'provider_id' => '123456789',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'provider',
                    'provider_id',
                    'avatar',
                    'created_at',
                    'updated_at',
                ],
                'token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'provider' => 'google',
            'provider_id' => '123456789',
        ]);
    }

    /** @test */
    public function it_requires_valid_provider()
    {
        $response = $this->postJson('/api/v1/auth/social/login', [
            'provider' => 'invalid_provider',
            'provider_id' => '123456789',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'error',
                'messages'
            ]);
    }
}