<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $medicines = require database_path('factories\sample_datas\product_samples.php');
        
        return [
            'generic_name' => fake()->unique()->randomElement($medicines['generic_names']),
            'brand_name' => fake()->unique()->randomElement($medicines['brand_names']),

            'form' => fake()->randomElement([
                'Oral',
                'Injectables'
            ]),

            'strength' => fake()->randomElement([
                'Weak', 'Strong'
            ]),
        ];
    }
}
