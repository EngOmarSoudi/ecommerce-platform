<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Promotion;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_promotions()
    {
        $promotion = Promotion::factory()->create([
            'name' => 'Summer Sale',
            'description' => '50% off on all summer items',
            'promotion_type' => 'discount',
            'discount_type' => 'percentage',
            'discount_value' => 50.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('promotions', [
            'name' => 'Summer Sale',
            'description' => '50% off on all summer items',
            'promotion_type' => 'discount',
            'discount_type' => 'percentage',
            'discount_value' => 50.00,
            'is_active' => true,
        ]);

        $this->assertEquals('Summer Sale', $promotion->name);
        $this->assertEquals(50.00, $promotion->discount_value);
    }

    /** @test */
    public function promotions_can_have_coupons()
    {
        $promotion = Promotion::factory()->create([
            'name' => 'Summer Sale',
        ]);

        $coupon1 = Coupon::factory()->create([
            'promotion_id' => $promotion->id,
            'code' => 'SUMMER50',
            'usage_limit_per_user' => 1,
            'is_active' => true,
        ]);

        $coupon2 = Coupon::factory()->create([
            'promotion_id' => $promotion->id,
            'code' => 'SUMMER25',
            'usage_limit_per_user' => 2,
            'is_active' => true,
        ]);

        $this->assertEquals(2, $promotion->coupons()->count());
        $this->assertTrue($promotion->coupons->contains($coupon1));
        $this->assertTrue($promotion->coupons->contains($coupon2));
        $this->assertEquals($coupon1->promotion->id, $promotion->id);
    }

    /** @test */
    public function promotions_can_be_applied_to_categories()
    {
        $promotion = Promotion::factory()->create([
            'name' => 'Electronics Sale',
        ]);

        $category1 = Category::factory()->create([
            'name' => 'Smartphones',
        ]);

        $category2 = Category::factory()->create([
            'name' => 'Laptops',
        ]);

        $promotion->categories()->attach([$category1->id, $category2->id]);

        $this->assertEquals(2, $promotion->categories()->count());
        $this->assertTrue($promotion->categories->contains($category1));
        $this->assertTrue($promotion->categories->contains($category2));
    }

    /** @test */
    public function promotions_can_be_applied_to_products()
    {
        $promotion = Promotion::factory()->create([
            'name' => 'Featured Product Discount',
        ]);

        $product1 = Product::factory()->create([
            'name' => 'iPhone 15 Pro',
        ]);

        $product2 = Product::factory()->create([
            'name' => 'MacBook Air',
        ]);

        $promotion->products()->attach([$product1->id, $product2->id]);

        $this->assertEquals(2, $promotion->products()->count());
        $this->assertTrue($promotion->products->contains($product1));
        $this->assertTrue($promotion->products->contains($product2));
    }
}