<?php

namespace App\Http\Resources\Admin;
use App\Models\Order;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

  	$distance        = Order::where('driver_id', '=', $this->id)->whereDate('created_at', Carbon::today())->sum('distance');

	$orders_delivered=Order::where('driver_id', '=', $this->id)->where('status','=', 'delivered')->count();
	$array = parent::toArray($request);
	$array ['users']= $this->users;
        $array ['orders']= $this->orders;
	$array ['distance']= $distance   ;
	$array ['orders_delivered']=$orders_delivered;
       
        // $this->orders()->with(['driver','restaurant']);
        // $this->acceptedOrders;
        // $this->rejectRequests;
        return $array;
    }
}
