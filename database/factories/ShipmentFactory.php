<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shipment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'tracking_number' => 'TRK' . strtoupper($this->faker->unique()->lexify('???')) . $this->faker->unique()->numerify('######'),
            'carrier' => $this->faker->randomElement(['FedEx', 'UPS', 'DHL', 'USPS']),
            'status' => $this->faker->randomElement(['pending', 'shipped', 'in_transit', 'delivered']),
            'shipped_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'actual_delivery_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'estimated_delivery_at' => $this->faker->optional()->dateTimeBetween('now', '+1 week'),
        ];
    }
}