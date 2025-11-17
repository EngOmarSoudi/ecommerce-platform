<?php

namespace Database\Factories;

use App\Models\OrderEvent;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderEvent>
 */
class OrderEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'event_type' => $this->faker->randomElement(['created', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
            'description' => $this->faker->sentence(),
            'metadata' => json_encode(['user_id' => $this->faker->numberBetween(1, 100)]),
        ];
    }
}