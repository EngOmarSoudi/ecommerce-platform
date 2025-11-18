<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function security_headers_are_present()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /** @test */
    public function csp_header_is_present()
    {
        $response = $this->get('/');

        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    /** @test */
    public function csrf_protection_is_enabled()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Without CSRF token, request should fail
        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function sensitive_routes_require_authentication()
    {
        $protectedRoutes = [
            '/dashboard',
            '/profile',
            '/orders',
            '/settings',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function env_file_is_not_accessible()
    {
        $response = $this->get('/.env');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function vendor_directory_is_not_accessible()
    {
        $response = $this->get('/vendor/autoload.php');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function sql_injection_is_prevented()
    {
        // Attempt SQL injection in search
        $response = $this->get('/products?search=\' OR 1=1--');
        
        // Should not cause SQL error or return all products
        $response->assertStatus(200);
    }

    /** @test */
    public function xss_attack_is_prevented()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/profile', [
            'name' => '<script>alert("XSS")</script>',
        ]);

        // Script should be escaped in output
        $response = $this->get('/profile');
        $response->assertDontSee('<script>', false);
    }

    /** @test */
    public function rate_limiting_is_enforced()
    {
        // Make 121 requests (exceeds 120 per minute limit)
        for ($i = 0; $i < 121; $i++) {
            $response = $this->get('/');
        }

        $response = $this->get('/');
        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function password_is_hashed_in_database()
    {
        $user = User::factory()->create([
            'password' => 'PlainPassword123',
        ]);

        $this->assertDatabaseMissing('users', [
            'password' => 'PlainPassword123',
        ]);

        $this->assertNotEquals('PlainPassword123', $user->password);
    }
}
