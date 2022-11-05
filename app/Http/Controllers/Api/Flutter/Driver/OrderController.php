<?php

namespace App\Http\Controllers\Api\Flutter\Driver;
use App\Http\Requests\Admin\SelectDriverRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Events\NotifyOrderStatusFromDriver;
use Illuminate\Http\Request;
Use App\Models\Order;
use App\Models\RejectRequest;
use Auth;

class OrderController extends Controller
{
    public function update(SelectDriverRequest $request){
        try{
            $order  = Order::findOrFail($request->order_id);

            if($request->status == 'accepted'){
                abort_if(!empty($order->driver_id) , 403 , 'Order has been assign for another driver!.');
                $check = Order::where(['driver_id' => Auth::guard('driver-api')->id() , 'status' => 'approved'])->get();
                abort_if($check->count() > 0 , 403 , 'you already accepted order !.');
                
                $order->driver_id   = Auth::guard('driver-api')->id();
                $order->status      = 'approved';
                $order->save();
            }elseif($request->status == 'delivered'){
                $order->status      = 'delivered';
                $order->save();
            }elseif($request->status == 'rejected'){
                RejectRequest::firstOrCreate([
                    'order_id'  => $request->order_id,
                    'driver_id' => Auth::guard('driver-api')->id(),
                    'reason'    => $request->reason,
                ]);
            }

            if($request->status != 'rejected'){
                event(new NotifyOrderStatusFromDriver(Auth::guard('driver-api')->id() , $order->id , $order->restaurant->user->id));
            }
            
            return new OrderResource($order);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);   
        }
    }
}
