<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_categories_with_subcategories()
    {
        $parentCategory = Category::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $childCategory = Category::factory()->create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'parent_id' => $parentCategory->id,
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'parent_id' => null,
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'parent_id' => $parentCategory->id,
        ]);

        $this->assertTrue($parentCategory->children->contains($childCategory));
        $this->assertEquals($childCategory->parent->id, $parentCategory->id);
    }

    /** @test */
    public function it_can_create_products_with_categories_and_brands()
    {
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $brand = Brand::factory()->create([
            'name' => 'Apple',
            'slug' => 'apple',
        ]);

        $product = Product::factory()->create([
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'sku_base' => 'IPH15PRO',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'sku_base' => 'IPH15PRO',
        ]);

        $this->assertEquals($product->category->id, $category->id);
        $this->assertEquals($product->brand->id, $brand->id);
    }

    /** @test */
    public function categories_can_have_multiple_products()
    {
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $products = Product::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $this->assertEquals(3, $category->products()->count());
        $this->assertTrue($category->products->contains($products[0]));
    }

    /** @test */
    public function brands_can_have_multiple_products()
    {
        $brand = Brand::factory()->create([
            'name' => 'Apple',
            'slug' => 'apple',
        ]);

        $products = Product::factory()->count(2)->create([
            'brand_id' => $brand->id,
        ]);

        $this->assertEquals(2, $brand->products()->count());
        $this->assertTrue($brand->products->contains($products[0]));
    }
}