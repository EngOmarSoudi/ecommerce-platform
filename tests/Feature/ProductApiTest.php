<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test fetching categories.
     */
    public function test_can_fetch_categories(): void
    {
        // Create some categories
        $categories = Category::factory()->count(3)->create();

        // Make request to categories endpoint
        $response = $this->get('/api/v1/categories');

        // Assert response status
        $response->assertStatus(200);
        
        // Assert we get the categories in the response
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Test fetching products.
     */
    public function test_can_fetch_products(): void
    {
        // Create a product with SKUs
        $product = Product::factory()->create();
        $skus = Sku::factory()->count(2)->create([
            'product_id' => $product->id,
        ]);

        // Make request to products endpoint
        $response = $this->get('/api/v1/products');

        // Assert response status
        $response->assertStatus(200);
        
        // Assert we get the product in the response
        $response->assertJsonFragment([
            'name' => $product->name,
        ]);
    }
}