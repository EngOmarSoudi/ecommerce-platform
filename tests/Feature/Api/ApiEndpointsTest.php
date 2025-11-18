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
        // Create some products
        $product1 = Product::factory()->create(['name' => 'Smartphone']);
        $product2 = Product::factory()->create(['name' => 'Laptop']);
        
        // Create SKUs for products
        Sku::factory()->create(['product_id' => $product1->id]);
        Sku::factory()->create(['product_id' => $product2->id]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'short_description',
                        'description',
                        'sku_base',
                        'status',
                        'is_featured',
                        'is_active',
                        'average_rating',
                        'review_count',
                        'created_at',
                        'updated_at',
                        'skus' => [
                            '*' => [
                                'id',
                                'product_id',
                                'sku',
                                'name',
                                'price',
                                'compare_at_price',
                                'cost_price',
                                'stock_quantity',
                                'low_stock_threshold',
                                'is_active',
                                'created_at',
                                'updated_at',
                            ]
                        ]
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ])
            ->assertJsonFragment([
                'name' => 'Smartphone'
            ])
            ->assertJsonFragment([
                'name' => 'Laptop'
            ]);
    }
}