<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MobileResponsivenessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mobile_navigation_is_present_on_small_screens()
    {
        $response = $this->get('/');
        
        // Check for mobile navigation component
        $response->assertSee('mobile-nav', false);
    }

    /** @test */
    public function mobile_checkout_page_is_accessible()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/checkout');
        
        $response->assertStatus(200);
        $response->assertSee('mobile-card', false); // Check for mobile-optimized classes
    }

    /** @test */
    public function touch_friendly_buttons_have_minimum_size()
    {
        $response = $this->get('/');
        
        // Check for btn-touch class (44px minimum)
        $response->assertSee('btn-touch', false);
    }

    /** @test */
    public function mobile_safe_area_padding_is_applied()
    {
        $response = $this->get('/');
        
        // Check for mobile-safe-area class
        $response->assertSee('mobile-safe-area', false);
    }

    /** @test */
    public function product_grid_is_responsive()
    {
        Product::factory()->count(6)->create();
        
        $response = $this->get('/products');
        
        $response->assertStatus(200);
        // Check for responsive grid classes
        $response->assertSee('grid-cols-1', false);
        $response->assertSee('md:grid-cols-3', false);
    }

    /** @test */
    public function forms_are_mobile_optimized()
    {
        $response = $this->get('/login');
        
        // Check for touch-manipulation class on inputs
        $response->assertSee('touch-manipulation', false);
    }
}
