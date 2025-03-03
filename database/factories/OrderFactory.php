<?php

namespace Database\Factories;

use App\Models\ExclusiveDeal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::pluck('id')->random();
        $userInDeal = ExclusiveDeal::pluck('user_id')->random();

        return [
            'user_id' => $user,
            'exclusive_deal_id' => ExclusiveDeal::where('user_id', $userInDeal)->pluck('id')->random(),
            'date_ordered' => fake()->dateTimeBetween('-1 Year', 'now'),
            'status' => fake()->randomElement([
                'pending',
                'completed',
                'cancelled',
                'partial-delivery',
                'delivered'
            ]),
            'quantity' => fake()->numberBetween(1, 15),
        ];
    }
}
