<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'short_description' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'seller_id' => null, // Will be set in tests
            'sku_base' => $this->faker->unique()->ean8(),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'is_featured' => $this->faker->boolean(10), // 10% chance of being featured
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'average_rating' => $this->faker->randomFloat(2, 0, 5),
            'review_count' => $this->faker->numberBetween(0, 100),
        ];
    }
}