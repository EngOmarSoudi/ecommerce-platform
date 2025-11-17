<?php

namespace Database\Factories;

use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Sku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'sku_id' => Sku::factory(),
            'movement_type' => $this->faker->randomElement(['in', 'out', 'adjustment']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'reference_type' => $this->faker->randomElement(['order', 'purchase_order', 'adjustment']),
            'reference_id' => $this->faker->uuid(),
            'notes' => $this->faker->sentence(),
        ];
    }
}