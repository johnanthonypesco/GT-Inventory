<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Historylogs;
use Illuminate\Support\Facades\Auth;

class HistorylogController extends Controller
{
    public function showHistorylog(Request $request){

        $historylogs = Historylogs::orderBy('created_at', 'desc')->get();
        return view('admin.historylog', ['historylogs' => $historylogs]);
    }

    public static function addproductlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    public static function addstocklog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }

    public static function deleteproductlog($event, $description){
        Historylogs::create([
            'event' => $event,
            'description' => $description,
            'user_email'=> auth()->user()->email,
            'created_at' => now()
        ]);
    }
}
