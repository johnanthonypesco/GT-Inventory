<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ExclusiveDeal;
use Illuminate\Database\Seeder;

class SeasonalDataSeeder extends Seeder
{
    public function run()
    {
        // Create seasonal products
        $rainySeasonProducts = Product::factory()
            ->count(10)
            ->create(['season_peak' => 'tag-ulan']);

        $summerSeasonProducts = Product::factory()
            ->count(10)
            ->create(['season_peak' => 'tag-init']);

        $allYearProducts = Product::factory()
            ->count(5)
            ->create(['season_peak' => 'all-year']);

        // Create seasonal orders
        $this->createSeasonalOrders($rainySeasonProducts, 'tag-ulan');
        $this->createSeasonalOrders($summerSeasonProducts, 'tag-init');
        $this->createSeasonalOrders($allYearProducts, 'all-year');
    }

    protected function createSeasonalOrders($products, $season)
    {
        $products->each(function($product) use ($season) {
            $orderCount = match($season) {
                'tag-ulan', 'tag-init' => rand(20, 40), // More orders during peak season
                default => rand(10, 20) // Fewer orders for all-year products
            };

            // Get all exclusive deals for this product
            $deals = $product->exclusiveDeals;
            
            if ($deals->isEmpty()) {
                // Create at least one deal if none exists
                $deals = collect([ExclusiveDeal::factory()->create(['product_id' => $product->id])]);
            }

            Order::factory()
                ->count($orderCount)
                ->delivered()
                ->create([
                    'exclusive_deal_id' => $deals->random()->id,
                    'date_ordered' => $this->getSeasonalDate($season)
                ]);
        });
    }

    protected function getSeasonalDate($season)
    {
        return match($season) {
            'tag-ulan' => now()->setMonth(rand(6, 9))->setDay(rand(1, 30)),
            'tag-init' => now()->setMonth(rand(3, 5))->setDay(rand(1, 30)),
            default => now()->subMonths(rand(1, 12))
        };
    }
}