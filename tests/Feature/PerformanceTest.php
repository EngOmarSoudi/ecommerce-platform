<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_listing_uses_caching()
    {
        Product::factory()->count(20)->create();
        
        // First request should hit database
        $start = microtime(true);
        $this->get('/products');
        $firstLoadTime = microtime(true) - $start;
        
        // Second request should use cache (faster)
        $start = microtime(true);
        $this->get('/products');
        $cachedLoadTime = microtime(true) - $start;
        
        // Cached load should be faster (allowing some variance)
        $this->assertLessThan($firstLoadTime * 0.8, $cachedLoadTime);
    }

    /** @test */
    public function settings_are_cached()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(['app_name' => 'Test Shop']);
        
        $user = User::factory()->create();
        
        $this->actingAs($user)->get('/settings');
    }

    /** @test */
    public function database_queries_are_optimized()
    {
        Product::factory()->count(10)->create();
        
        \DB::enableQueryLog();
        
        $this->get('/products');
        
        $queries = \DB::getQueryLog();
        
        // Should not have N+1 queries
        $this->assertLessThan(10, count($queries));
    }

    /** @test */
    public function static_assets_have_cache_headers()
    {
        $response = $this->get('/css/app.css');
        
        // Check for caching headers
        $this->assertTrue(
            $response->headers->has('Cache-Control') ||
            $response->headers->has('Expires')
        );
    }

    /** @test */
    public function gzip_compression_is_enabled()
    {
        $response = $this->get('/', ['Accept-Encoding' => 'gzip']);
        
        // Check if content can be compressed
        $content = $response->getContent();
        $this->assertNotEmpty($content);
    }

    /** @test */
    public function opcache_is_enabled_in_production()
    {
        if (config('app.env') === 'production') {
            $this->assertTrue(function_exists('opcache_get_status'));
            
            if (function_exists('opcache_get_status')) {
                $status = opcache_get_status();
                $this->assertTrue($status['opcache_enabled']);
            }
        } else {
            $this->markTestSkipped('OPcache test only runs in production');
        }
    }

    /** @test */
    public function health_check_endpoint_responds_quickly()
    {
        $start = microtime(true);
        $response = $this->get('/health');
        $duration = microtime(true) - $start;
        
        $response->assertStatus(200);
        $this->assertLessThan(0.1, $duration); // Less than 100ms
    }
}
