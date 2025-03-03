<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exclusive_Deal>
 */
class Exclusive_DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'user_id'=> User::pluck("id")->random(),
            'product_id' => Product::pluck("id")->random(),
            'price' => fake()->numberBetween(1, 1_000),
            'deal_type' => fake()->randomElement([
                'discount', 'freebie'
            ]),
        ];
    }
}
