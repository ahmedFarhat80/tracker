<?php

namespace App\Http\Controllers\Api\Restaurant;


use App\Http\Resources\Admin\OrderResource;
use App\Http\Requests\Admin\OrderRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\SelectDriverRequest;
use App\Http\Requests\Admin\FetchNearestdriversRequest;
use App\Services\FindNearestDriverService;
use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use App\Notifications\DriverNotification;
use App\Events\NotifyOrderDrivers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Driver;
use DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data = Order::where('restaurant_id' , auth()->id())->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return OrderResource::collection($data);        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(OrderRequest $request){
        try{
            $order = $this->process(new Order , $request);
            if($order){
                $longitude  = $order->restaurant->lon ; //31.377033;
                $latitude   = $order->restaurant->lat; //30.016893;
                $radius     = 3; // far KM "6371"
                $user_id    = $order->restaurant->user_id;
                
                $order->nearestdrivers = FindNearestDriverService::fetch(
                        $longitude , $latitude , $radius , $user_id
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
            }else{
                return errorMessage(__('dashboard.error'), 500);
            }
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            $data       = Order::findOrFail($id);
            $longitude  = $data->restaurant->lon ; //31.377033;
            $latitude   = $data->restaurant->lat; //30.016893;
            $radius     = 3; // far KM "6371"
            $user_id    = $data->restaurant->user_id;
            // check status of order
            abort_if($data->status == 'approved' || $data->status == 'delivered', 403, 'You can\'t update this order.');

            /* Check restaurant request id is the same loged in id */  
            // if (Gate::denies('restaurant-update-order', $request))
            //     return errorMessage('Forbidden Error: order status not pending!', 403);
        
            $order  = $this->process($data , $request);
            $order->nearestdrivers = FindNearestDriverService::fetch(
                    $longitude , $latitude , $radius , $user_id
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



    /**
     * store / update patients fields
     *
     * @param  order Object , Request
     * @return order
     */
    protected function process(Order $order , Request $request){
        $order->restaurant_id       = \Auth::guard('restaurant-api')->user()->id;
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function status(Request $request , $id)
    {
        try {
            $data = Order::findOrFail($id);
            
            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status ]);

            return new OrderResource($data);            

        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch nearest drivers
     *
     * @param  int  $order_id
     * @return \Illuminate\Http\Response
    */

    public function fetchNearestdrivers(FetchNearestdriversRequest $request){
        $order      = Order::findOrFail($request->order_id);
        $user_id    = $order->restaurant->user_id ?? null ;
        $longitude  = $order->lon;
        $latitude   = $order->lat;
        $radius     = 3;

        $order->nearestdrivers = FindNearestDriverService::fetch(
                $longitude , $latitude , $radius , $user_id 
            )->get();
            
        return new OrderResource($order);   
    }
}
