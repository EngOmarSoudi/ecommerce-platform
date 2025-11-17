<?php

namespace Database\Factories;

use App\Models\SellerStore;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SellerStore>
 */
class SellerStoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SellerStore::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'seller_id' => Seller::factory(),
            'store_name' => $this->faker->company(),
            'store_slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'logo_url' => $this->faker->imageUrl(200, 200),
            'cover_image_url' => $this->faker->imageUrl(800, 400),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}