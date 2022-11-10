<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Requests\Shipping\OrderRequest;
use App\Services\FirebaseService;
use App\Services\FindNearestDriverService;
use App\Notifications\DriverNotification;
use App\Events\NotifyOrderDrivers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\user ;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data   = User::find(\Auth::guard('user-api')->id())->restaurants()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return response($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(OrderRequest $request){
        try{
            $user        = \Auth::guard('user-api')->user();

            // check relation between user & restaurant
            if(!$user->hasRestaurant($request->restaurant_id))
                return errorMessage("This restauant not have relation with logged shipping!", 500);

            $order = $this->process(new Order , $request);

            $longitude      = $order->restaurant->lon ; //31.377033;
            $latitude       = $order->restaurant->lat; //30.016893;
            $radius         = 3; // far KM "6371"

            $order->nearestdrivers = FindNearestDriverService::fetch(
                    $longitude , $latitude , $radius , $user->id
                )->get();

            $tokens = $order->nearestdrivers->whereNotNull('fcm_token')
                                            ->pluck('fcm_token')
                                            ->toArray();
            /**
             * insert driver notification in DB
             * push for driver channel via pusher
             */
            foreach($order->nearestdrivers  as $driver){
                $driver->notify(new DriverNotification($driver));
                event(new NotifyOrderDrivers($driver->id , $order->id));
            }

            // SEND NOTIFICSTION for nearest driver
            FirebaseService::sendNotification($tokens , 'New order request');

            return new OrderResource($order);
        } catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderRequest $request, $id){
        try{
            $user  = \Auth::guard('user-api')->user();
            // check relation between user & restaurant
            if(!$user->hasRestaurant($request->restaurant_id))
                return errorMessage("This restauant not have relation with logged shipping!", 500);

            $data           = Order::findOrFail($id);

            // check status of order
            abort_if($data->status == 'approved' || $data->status == 'delivered', 403, 'You can\'t update this order.');

            $order          = $this->process($data , $request);
            $longitude      = $order->restaurant->lon ; //31.377033;
            $latitude       = $order->restaurant->lat; //30.016893;
            $radius         = 3; // far KM "6371"

            $order->nearestdrivers = FindNearestDriverService::fetch(
                    $longitude , $latitude , $radius , $user->id
                )->get();
            $tokens = $order->nearestdrivers->whereNotNull('fcm_token')
                                            ->pluck('fcm_token')
                                            ->toArray();
            /**
             * push for driver channel via pusher
             */
            foreach($order->nearestdrivers  as $driver){
                event(new NotifyOrderDrivers($driver->id , $order->id));
            }

            // SEND NOTIFICSTION for nearest driver
            FirebaseService::sendNotification($tokens , 'New order request');

            return new OrderResource($order);

        } catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $data = Order::findOrFail($id);
            return new OrderResource($data);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * store / update patients fields
     *
     * @param  order Object , Request
     * @return order
     */
    protected function process(Order $order , Request $request){
        try{
            $order->restaurant_id       = $request->restaurant_id;
            $order->client_name         = $request->client_name;
            $order->order_no            = $request->order_no;
            $order->details             = $request->details;
            $order->address             = $request->address;
            $order->lon                 = $request->lon;
            $order->lat                 = $request->lat;
            $order->mobile              = $request->mobile;
            $order->price               = $request->price;
            $order->duration            = $request->duration;
            $order->distance            = $request->distance;
            $order->paid_status         = $request->paid_status;

            $order->flat                = $request->flat;
            $order->building            = $request->building;
            $order->floor               = $request->floor;
            $order->street              = $request->street;
            $order->flat_type           = $request->flat_type;
            $order->fare                = $request->fare;
            $order->totalWithFare       = $request->totalWithFare;

            $order->save();

            return $order;
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            $data = Order::findOrFail($id);

            $data->delete();

            $data = [
                'status'    => 'success',
                'message'   => __('dashboard.delete_success'),
            ];

            return response($data, 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
