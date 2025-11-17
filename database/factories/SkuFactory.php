<?php

namespace Database\Factories;

use App\Models\Sku;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sku>
 */
class SkuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sku::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => $this->faker->unique()->ean13(),
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'compare_at_price' => $this->faker->randomFloat(2, 1, 1000),
            'cost_price' => $this->faker->randomFloat(2, 1, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'low_stock_threshold' => $this->faker->numberBetween(0, 10),
            'weight' => $this->faker->randomFloat(2, 0.1, 50),
            'length' => $this->faker->randomFloat(2, 1, 100),
            'width' => $this->faker->randomFloat(2, 1, 100),
            'height' => $this->faker->randomFloat(2, 1, 100),
            'dimensions' => $this->faker->word(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'discontinued']),
            'is_trackable' => $this->faker->boolean(90), // 90% chance of being trackable
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}