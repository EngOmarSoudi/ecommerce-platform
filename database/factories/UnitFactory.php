<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'symbol' => $this->faker->unique()->lexify('???'),
            'description' => $this->faker->sentence(),
            'conversion_factor' => $this->faker->randomFloat(4, 0.0001, 100),
            'is_base_unit' => $this->faker->boolean(20), // 20% chance of being base unit
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}