<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $sellerUser;
    protected User $customerUser;
    protected User $supportUser;
    protected string $adminToken;
    protected string $sellerToken;
    protected string $customerToken;
    protected string $supportToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator']);
        $sellerRole = Role::create(['name' => 'seller', 'description' => 'Seller']);
        $customerRole = Role::create(['name' => 'customer', 'description' => 'Customer']);
        $supportRole = Role::create(['name' => 'support', 'description' => 'Support']);
        
        // Create users with different roles
        $this->adminUser = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@example.com']);
        $this->adminUser->roles()->attach($adminRole);
        $this->adminToken = $this->adminUser->createToken('auth-token')->plainTextToken;
        
        $this->sellerUser = User::factory()->create(['name' => 'Seller User', 'email' => 'seller@example.com']);
        $this->sellerUser->roles()->attach($sellerRole);
        $this->sellerToken = $this->sellerUser->createToken('auth-token')->plainTextToken;
        
        $this->customerUser = User::factory()->create(['name' => 'Customer User', 'email' => 'customer@example.com']);
        $this->customerUser->roles()->attach($customerRole);
        $this->customerToken = $this->customerUser->createToken('auth-token')->plainTextToken;
        
        $this->supportUser = User::factory()->create(['name' => 'Support User', 'email' => 'support@example.com']);
        $this->supportUser->roles()->attach($supportRole);
        $this->supportToken = $this->supportUser->createToken('auth-token')->plainTextToken;
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    /** @test */
    public function seller_can_access_seller_routes()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->sellerToken)
            ->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    /** @test */
    public function customer_can_access_customer_routes()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->customerToken)
            ->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    /** @test */
    public function support_can_access_support_routes()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->supportToken)
            ->getJson('/api/v1/me');

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401);
    }
}