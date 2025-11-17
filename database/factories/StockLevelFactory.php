<?php

namespace Database\Factories;

use App\Models\StockLevel;
use App\Models\Warehouse;
use App\Models\Sku;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockLevel>
 */
class StockLevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockLevel::class;

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
            'quantity_on_hand' => $this->faker->numberBetween(0, 1000),
            'quantity_reserved' => $this->faker->numberBetween(0, 100),
            'reorder_point' => $this->faker->numberBetween(10, 50),
            'max_stock_level' => $this->faker->numberBetween(500, 2000),
        ];
    }
}