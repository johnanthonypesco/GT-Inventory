<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'form' => fake()->randomElement(['Oral', 'Injectables']),
            'strength' => fake()->randomElement(['Weak', 'Strong']),
            'season_peak' => fake()->randomElement(['tag-ulan', 'tag-init', 'all-year']),
            'trend_score' => fake()->numberBetween(0, 1000),
        ];
    }

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

    // State methods for specific seasons
    public function rainySeason()
    {
        return $this->state(function (array $attributes) {
            return [
                'season_peak' => 'tag-ulan',
            ];
        });
    }

    public function summerSeason()
    {
        return $this->state(function (array $attributes) {
            return [
                'season_peak' => 'tag-init',
            ];
        });
    }

    public function allYear()
    {
        return $this->state(function (array $attributes) {
            return [
                'season_peak' => 'all-year',
            ];
        });
    }
}