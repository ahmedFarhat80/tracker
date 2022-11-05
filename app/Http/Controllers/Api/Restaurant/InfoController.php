<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\OrderFilterRequest;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Order;
use Carbon\Carbon;

class InfoController extends Controller
{
    public function info(){
        $id   = \Auth::guard('restaurant-api')->id();

        $data =  Restaurant::withCount('orders')
                            ->withCount(['orders AS total_pending' => function($q){
                                $q->where('status', 'pending');
                            }])
                            ->withCount(['orders AS total_approved' => function($q){
                                $q->where('status', 'approved');
                            }])
                            ->withCount(['orders AS total_delivered' => function($q){
                                $q->where('status', 'delivered');
                            }])
                            ->withCount(['orders AS total_price' => function ($q) {
                                    $q->select(\DB::raw("SUM(price) as total_price"));
                            }])
                            ->withCount(['orders AS total_delivered_price' => function ($q) {
                                    $q->select(\DB::raw("SUM(price) as total_delivered_price"))
                                        ->where('status', 'delivered');
                            }])
                            ->withCount(['orders AS total_pending_price' => function ($q) {
                                    $q->select(\DB::raw("SUM(price) as total_pending_price"))
                                        ->where('status', 'pending');
                            }])
                            ->withCount(['orders AS total_fare' => function ($q) {
                                    $q->select(\DB::raw("SUM(fare) as total_fare"));                                        
                            }])
                            ->withCount(['orders AS total_price_with_fare' => function ($q) {
                                    $q->select(\DB::raw("SUM(totalWithFare) as total_price_with_fare"));                                        
                            }])
                            ->findorFail($id);
        

        return response()->json($data);
    }

    public function ordersFilter(OrderFilterRequest $request){
        $id     = \Auth::guard('restaurant-api')->id();

        $dateS  = new Carbon(date($request->from));
        $dateE  = new Carbon(date($request->to));

        $data   = Order::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])
                        ->where('restaurant_id' , $id)
                        ->get();

        return response()->json($data);
    }
}
