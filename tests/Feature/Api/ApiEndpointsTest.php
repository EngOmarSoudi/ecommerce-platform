<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_categories()
    {
        // Create some categories
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Clothing']);

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Electronics'
            ])
            ->assertJsonFragment([
                'name' => 'Clothing'
            ]);
    }

    /** @test */
    public function it_can_get_products_with_pagination()
    {
        // Create some products with active status
        $product1 = Product::factory()->create([
            'name' => 'Smartphone',
            'status' => 'active',
            'is_active' => true
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Laptop',
            'status' => 'active',
            'is_active' => true
        ]);
        
        // Create SKUs for products
        Sku::factory()->create([
            'product_id' => $product1->id,
            'is_active' => true
        ]);
        Sku::factory()->create([
            'product_id' => $product2->id,
            'is_active' => true
        ]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
        
        // Verify pagination structure
        $data = $response->json();
        $this->assertIsArray($data['data']);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertArrayHasKey('total', $data['pagination']);
    }
}