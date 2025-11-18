<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhoneAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create customer role
        Role::create(['name' => 'customer', 'description' => 'Customer']);
    }

    /** @test */
    public function it_can_request_otp_for_phone_authentication()
    {
        $response = $this->postJson('/api/v1/auth/phone/request-otp', [
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'OTP sent successfully',
                'phone' => '+1234567890'
            ]);
    }

    /** @test */
    public function it_can_verify_otp_and_register_new_user()
    {
        $response = $this->postJson('/api/v1/auth/phone/verify-otp', [
            'phone' => '+1234567890',
            'otp' => '123456',
            'name' => 'John Doe',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
                'token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'phone' => '+1234567890',
        ]);
    }

    /** @test */
    public function it_can_verify_otp_and_login_existing_user()
    {
        // Create a user first
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
        ]);

        $response = $this->postJson('/api/v1/auth/phone/verify-otp', [
            'phone' => '+1234567890',
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
                'token',
                'token_type'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'phone' => '+1234567890',
        ]);
    }

    /** @test */
    public function it_cannot_verify_invalid_otp()
    {
        $response = $this->postJson('/api/v1/auth/phone/verify-otp', [
            'phone' => '+1234567890',
            'otp' => '000000',
            'name' => 'John Doe',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Invalid OTP'
            ]);
    }
}