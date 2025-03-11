<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Inventory;
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
        $company_id = Company::pluck("id")->random();
        $company_location = Company::where('id', $company_id)->pluck('location_id')->random();
        $product_id = Inventory::where('location_id', $company_location)->pluck('product_id')->random();

        return [
            'company_id'=> $company_id,
            'product_id' => $product_id,
            // 'product_id' => Product::pluck("id")->random(),
            'price' => fake()->numberBetween(1, 1_000),
            'deal_type' => fake()->randomElement([
                'discounted',
                'regular',
            ]),
        ];
    }
}
