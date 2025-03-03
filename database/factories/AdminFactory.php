<?php

namespace Database\Factories;

use App\Models\SuperAdmin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'super_admin_id' => SuperAdmin::pluck('id')->random(),
            'username' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(8),
            'is_admin' => true,
        ];
    }
}
