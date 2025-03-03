<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::pluck("id")->random(),

            'batch_number' => strtoupper(
                fake()
                ->bothify('BATCH-NO-#??#?#')
            ),
            'expiry_date' => fake()->dateTimeBetween('-3 months', '+2 years'),
            'quantity' => fake()->numberBetween(1, 500),
        ];
    }
}
