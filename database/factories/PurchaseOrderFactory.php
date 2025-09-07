<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $PONum = fake()->unique()->numberBetween(1, 100);
        $companyID = Company::pluck('id')->random();

        return [
            'company_id' => $companyID,
            'po_number' => $PONum,
        ];
    }
}
