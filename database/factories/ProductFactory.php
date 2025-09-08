<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ProductFactory extends Factory
{
    protected int $perPair = 2;

    public function definition(): array
    {
        return [
            // 'season_peak' => fake()->randomElement(['tag-ulan', 'tag-init', 'all-year']),
            // 'trend_score' => fake()->numberBetween(0, 1000),
        ];
    }

    public function configure(): static
    {
        $medicines   = require database_path('factories/sample_datas/product_samples.php');
        $allCombos   = [];
        $forms       = ['Oral', 'Injectables'];
        $strengths   = ['12mg', '65mg'];

        foreach ($medicines['generic_names'] as $i => $generic) {
            $brand = $medicines['brand_names'][$i] ?? null;

            // build all possible form×strength combos for this pair
            $pairCombos = [];
            foreach ($forms as $form) {
                foreach ($strengths as $strength) {
                    $pairCombos[] = [
                        'generic_name' => $generic,
                        'brand_name'   => $brand,
                        'form'         => $form,
                        'strength'     => $strength,
                    ];
                }
            }

            // shuffle and take only $perPair combos
            shuffle($pairCombos);
            $selected = array_slice($pairCombos, 0, $this->perPair);

            // merge in the random fields and accumulate
            foreach ($selected as $combo) {
                $allCombos[] = $combo + $this->definition();
            }
        }

        // feed into sequence for exactly count(generic) * perPair records
        return $this->sequence(...$allCombos);
    }

    public function perPair(int $n): static
    {
        $this->perPair = $n;
        return $this;
    }

    // State methods for specific seasons
    // public function rainySeason()
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'season_peak' => 'tag-ulan',
    //         ];
    //     });
    // }

    // public function summerSeason()
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'season_peak' => 'tag-init',
    //         ];
    //     });
    // }

    // public function allYear()
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'season_peak' => 'all-year',
    //         ];
    //     });
    // }
}