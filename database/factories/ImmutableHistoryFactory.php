<?php

namespace Database\Factories;

use App\Models\ExclusiveDeal;
use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ImmutableHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::with('company.location')->get();
        $userID = $user->pluck('id')->random();
        $usableUser = $user->find($userID);
        $province = $usableUser->company->location->province;
        $companyName = $usableUser->company->name;
        $employeeName = $usableUser->name;

        $orderID = Order::where('user_id', '=', $userID)
        ->whereIn('status', ['delivered', 'cancelled'] )->pluck('id')->random();
        $usableOrder = Order::with('exclusive_deal.product')->findOrFail($orderID);
        $dateOrdered = $usableOrder->date_ordered;
        $status = $usableOrder->status;

        $usableDeal = ExclusiveDeal::with('product')->find($usableOrder->exclusive_deal_id);

        $product = $usableOrder->exclusive_deal->product;
        $generic = $product->generic_name;
        $brand = $product->brand_name;
        $form = $product->form;
        $strength = $product->strength;
        $quantity = $usableOrder->id;
        $price = $usableDeal->price;
        $subtotal = $usableDeal->price * $quantity;


        // Hula ko mapapapunta si kuya dito sa factory nato dahil nag loloko deduction tracking 
        // sa order history table hahah
        // --by: Evil Sigrae >:)

        return [
            'order_id' => $orderID,
            'province' => $province,
            'company' => $companyName,
            'employee' => $employeeName,
            'date_ordered' => $dateOrdered,
            'status' => $status,
            'generic_name' => $generic,
            'brand_name' => $brand,
            'form' => $form,
            'strength' => $strength,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal,
        ];
    }
}
