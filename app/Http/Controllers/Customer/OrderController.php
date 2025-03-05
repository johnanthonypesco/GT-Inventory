<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ExclusiveDeal;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function showOrder(){
        $deals = ExclusiveDeal::where('company_id', auth('web')->user()->company_id)
        ->with(['product'])
        ->get();

        // dd($deals->toArray());

        return view('customer.order', [
            'listedDeals' => $deals,
        ]);
    }
}
