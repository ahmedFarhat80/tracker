<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Wallet;
use Illuminate\Http\Request;

class NewController extends Controller
{
    //
    public function get_Resturant_data($id)
    {
        # code...

        $data         = Restaurant::where('id', '=', $id)->with('orders')->first();
        $count_orders = $data->orders->count();
        $Wallet       = Wallet::where('restaurant_id', '=', $id)->get();
        $Balance      = Wallet::where('restaurant_id', '=', $id)->count();

        return response()->json([
            'data'          => $data,
            'Wallet'        => $Wallet,
            "Balance"       => $Balance,
            "count_orders"  =>   $count_orders
        ]);
    }
}
