<?php

namespace Database\Seeders;

use App\Models\ExclusiveDeal;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::create([
            'user_id' => 69,
            'exclusive_deal_id' => ExclusiveDeal::where('company_id', 1)->pluck('id')->random(),
            'date_ordered' => fake()->dateTimeBetween('-1 day', 'now'),
            'status' => fake()->randomElement([
                'pending',
                'packed',
                // 'cancelled',
                // 'delivered',
            ]),
            'quantity' => fake()->numberBetween(1, 150),
        ]);
    }
}
