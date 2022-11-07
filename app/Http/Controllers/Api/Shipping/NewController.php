<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Wallet;
use Carbon\Carbon;
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


    public function get_driver_data($id)
    {
        $data  = Driver::where('id', '=', $id)->with('orders')->first();
        // $restaurant      =

        $Order_sum_price = Order::where('driver_id', '=', $id)->whereDate('created_at', Carbon::today())->where('paid_status', '=', "cash")->sum('price');
        $distance        = Order::where('driver_id', '=', $id)->whereDate('created_at', Carbon::today())->sum('distance');

        $Orders = Order::where('driver_id', '=', $id)->with('restaurant')->get();


        return response()->json([
            'data'  => $data,
            'Order_sum_price'  => $Order_sum_price,
            "distance" => $distance,
            "Orders" => $Orders
        ]);
    }
}
