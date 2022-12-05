<?php

namespace App\Http\Resources\Admin;

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
	 $array = parent::toArray($request);
	$array ['users']= $this->users;
        $array ['orders']= $this->orders;
       
        // $this->orders()->with(['driver','restaurant']);
        // $this->acceptedOrders;
        // $this->rejectRequests;
        return $array;
    }
}
