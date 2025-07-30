<?php

namespace Database\Factories;

use App\Models\ExclusiveDeal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $user = User::pluck('id')->random();
        $company_id = User::findOrFail($user)->company_id;

        return [
            'user_id' => $user,
            'exclusive_deal_id' => ExclusiveDeal::where('company_id', $company_id)->pluck('id')->random(),
            'date_ordered' => fake()->dateTimeBetween('-1 Year', 'now'),
            'status' => fake()->randomElement([
                'pending',
                'packed',
                // 'cancelled',
                // 'delivered'
            ]),
            'quantity' => fake()->numberBetween(1, 15),
        ];
    }

    // State methods for different statuses
    public function delivered()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'delivered',
            ];
        });
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }

    // State method for seasonal orders
    public function seasonal($season)
{
    return $this->state(function (array $attributes) use ($season) {
        $year = fake()->numberBetween(now()->year - 1, now()->year);
        $month = match($season) {
            'tag-init' => fake()->numberBetween(3, 5),
            'tag-ulan' => fake()->numberBetween(6, 9),
            default => fake()->numberBetween(1, 12),
        };
        $day = fake()->numberBetween(1, 28);

        return [
            'date_ordered' => now()->setDate($year, $month, $day)
        ];
    });
}

}