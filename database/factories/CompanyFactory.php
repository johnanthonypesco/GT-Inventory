<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => Location::pluck('id')->random(),
            'name' => fake()->unique()->randomElement([
                'Accenture Co.',
                'Fake Jeep Inc.',
                'Grassers Co.',
                'CornHud Inc.',
            ]),

            'address' => fake()->address(),
            'status' => fake()->randomElement(['active', 'closed']),
        ];
    }
}
