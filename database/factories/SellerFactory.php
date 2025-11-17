<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seller>
 */
class SellerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Seller::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => $this->faker->company(),
            'business_license_number' => $this->faker->unique()->numerify('BL-######'),
            'tax_id' => $this->faker->unique()->numerify('TAX-######'),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'suspended']),
            'rejection_reason' => $this->faker->optional()->sentence(),
        ];
    }
}