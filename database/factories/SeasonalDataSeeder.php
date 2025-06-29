<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Order;
use App\Models\ExclusiveDeal;

class SeasonalDataSeeder extends Seeder
{
    public function run()
    {
        // Tag-ulan products
        $rainySeasonProducts = Product::factory()->count(10)->rainySeason()->create();
        // Tag-init products
        $summerSeasonProducts = Product::factory()->count(10)->summerSeason()->create();
        // All-year products
        $allYearProducts = Product::factory()->count(5)->allYear()->create();

        // Create deals and orders per category
        $this->seedProductsWithOrders($rainySeasonProducts, 'tag-ulan');
        $this->seedProductsWithOrders($summerSeasonProducts, 'tag-init');
        $this->seedProductsWithOrders($allYearProducts, 'all-year');
    }

    protected function seedProductsWithOrders($products, $season)
    {
        $products->each(function ($product) use ($season) {
            // Create 1–2 exclusive deals per product
            $deals = ExclusiveDeal::factory()->count(rand(1, 2))->create([
                'product_id' => $product->id,
            ]);

            foreach ($deals as $deal) {
                // 🔁 Orders in PEAK season (simulate historical peak)
                Order::factory()
                    ->count(rand(15, 30))
                    ->delivered()
                    ->seasonal($season)
                    ->create(['exclusive_deal_id' => $deal->id]);

                // 🔁 Orders in OFF-SEASON (simulate current activity outside peak)
                Order::factory()
                    ->count(rand(5, 15))
                    ->delivered()
                    ->seasonal($this->randomOffSeason($season))
                    ->create(['exclusive_deal_id' => $deal->id]);

                // 🔁 Pending or Cancelled Orders for realism
                Order::factory()
                    ->count(rand(3, 6))
                    ->state(fn () => ['status' => fake()->randomElement(['pending', 'cancelled'])])
                    ->seasonal($this->randomOffSeason($season))
                    ->create(['exclusive_deal_id' => $deal->id]);
            }
        });
    }

    protected function randomOffSeason($current)
    {
        return match($current) {
            'tag-init' => fake()->randomElement(['tag-ulan', 'all-year']),
            'tag-ulan' => fake()->randomElement(['tag-init', 'all-year']),
            default => fake()->randomElement(['tag-ulan', 'tag-init']),
        };
    }
}
