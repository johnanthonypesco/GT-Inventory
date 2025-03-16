<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

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
        return [
            'form' => fake()->randomElement(['Oral', 'Injectables']),
            'strength' => fake()->randomElement(['Weak', 'Strong']),
        ];
    }

    /**
     * Configure the factory sequence.
     */
    public function configure(): static
    {
        $medicines = require database_path('factories/sample_datas/product_samples.php');

        return $this->sequence(
            ...array_map(fn($generic, $brand) => [
                'generic_name' => $generic,
                'brand_name' => $brand,
            ], $medicines['generic_names'], $medicines['brand_names'])
        );
    }
}
