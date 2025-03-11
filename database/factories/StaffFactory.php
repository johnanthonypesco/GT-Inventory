<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => Admin::pluck('id')->random(),
            'location_id' => Location::pluck('id')->random(),
            'staff_username' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(8),
            'is_staff' => true,
        ];
    }
}
