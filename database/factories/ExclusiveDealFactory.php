<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExclusiveDeal>
 */
class ExclusiveDealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id'=> Company::pluck("id")->random(),
            'product_id' => Product::pluck("id")->random(),
            'price' => fake()->numberBetween(1, 1_000),
            'deal_type' => fake()->randomElement([
                'discount', 'freebie'
            ]),
        ];
    }
}
