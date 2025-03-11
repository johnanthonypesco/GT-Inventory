<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'province' => fake()->unique()->randomElement(['Tarlac', 'Nueva Ecija']),
            'city' => fake()->unique()->randomElement(['Tarlac cty', 'Nueva Ecija cty']),
        ];
    }
}
