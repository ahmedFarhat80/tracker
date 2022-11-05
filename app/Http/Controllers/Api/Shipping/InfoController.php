<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Requests\Restaurant\OrderFilterRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForeignTransaction;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\User;

class InfoController extends Controller
{
    public function info(){
        $id   = \Auth::guard('user-api')->id();

        $data = User::with(['restaurants' => function($q){
                    $q->withCount('orders')
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
                        }]);
                }])
                ->withCount('restaurants')
                ->findOrFail($id);

        return response()->json($data);
    }

    public function transactionInfo(){
        $id   = \Auth::guard('user-api')->id();

        $data = Transaction:: where('user_id' , $id)->get();

        return response()->json($data);
    }

    public function foreignTransactionInfo(){
        $id   = \Auth::guard('user-api')->id();

        $data = ForeignTransaction:: where('user_id' , $id)->get();

        return response()->json($data);
    }

    public function ordersFilter(OrderFilterRequest $request){
        $user_id    = \Auth::guard('user-api')->id();

        $dateS      = new Carbon(date($request->from));
        $dateE      = new Carbon(date($request->to));

        $data       = Order::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])
                            ->whereHas('restaurant', function ($query) use ($user_id){
                                $query->where('user_id', $user_id);
                            })
                            ->get();

        return response()->json($data);
    }
}
