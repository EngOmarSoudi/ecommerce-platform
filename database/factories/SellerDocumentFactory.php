<?php

namespace Database\Factories;

use App\Models\SellerDocument;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SellerDocument>
 */
class SellerDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SellerDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'seller_id' => Seller::factory(),
            'document_type' => $this->faker->randomElement(['business_license', 'tax_id', 'id_proof', 'bank_details']),
            'file_path' => 'documents/' . $this->faker->word() . '.pdf',
            'is_verified' => $this->faker->boolean(70), // 70% chance of being verified
            'verified_at' => $this->faker->optional()->dateTime(),
            'rejection_reason' => $this->faker->optional()->sentence(),
        ];
    }
}