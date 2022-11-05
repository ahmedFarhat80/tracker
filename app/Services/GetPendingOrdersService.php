<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Order;

class GetPendingOrdersService
{
    /*
    * @param1 : pass current latitude of the restaurant
    * @param2 : pass current longitude of the restaurant
    * @param3: pass the radius in KM within how much distance you wanted to fiter
    * replace 6371 with 6371000 for meter OR 3956 for miles
    */
    public static function fetch($longitude , $latitude , $radius = 3 , $users_id = []){

        // $longitude  = 31.377033;
        // $latitude   = 30.016893;
        // $radius     = 100; // far KM "6371"
        // $user_id    = $driver->users->id;

        return Order::when($users_id, function($q) use ($users_id){
                        return $q->whereHas('restaurant' , function($q) use ($users_id){
                            $q->whereHas('user' , function($q) use ($users_id){
                                return $q->whereIn('id' , $users_id);
                            });
                        });
                    })
                    ->whereHas('restaurant' , function($q) use ($latitude, $longitude , $radius){
                        return $q->selectRaw("restaurants.id as restaurant_id,lon,lat,
                        ( 6371 * acos( cos( radians(?) ) *
                        cos( radians( lat ) )
                        * cos( radians( lon ) - radians(?)
                        ) + sin( radians(?) ) *
                        sin( radians( lat ) ) )
                        ) AS distance", [$latitude, $longitude, $latitude])
                        ->having("distance", "<", $radius);
                    })
                    ->whereNull('driver_id')
                    ->where('status' , 'pending')
                    ->whereDoesntHave('rejecetd' , function($q){
                        $q->where('driver_id' , \Auth::guard('driver-api')->id());
                    })
                    ->orderBy("distance",'asc')
                    // ->offset(0)
                    ->with('restaurant');
    }
}

