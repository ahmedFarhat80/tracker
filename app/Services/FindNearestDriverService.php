<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Driver;
use Carbon\Carbon;

class FindNearestDriverService
{
    /*
    * @param1 : pass current latitude of the restaurant
    * @param2 : pass current longitude of the restaurant
    * @param3: pass the radius in KM within how much distance you wanted to fiter
    * replace 6371 with 6371000 for meter OR 3956 for miles
    */
    public static function fetch($longitude , $latitude , $radius = 3 , $user_id = null){

        // $longitude  = 31.377033;
        // $latitude   = 30.016893;
        // $radius     = 100; // far KM "6371"
        // $user_id    = 1; // $order->restaurant->user_id

        if(\Auth::guard('user-api')->check()){
            $id     = \Auth::guard('user-api')->id();
            $slots  = getSettingsForShipping($id);

            foreach($slots as $slot){
                $start  = date_create_from_format('h:i:s', $slot->time_from);
                $end    = date_create_from_format('h:i:s', $slot->time_to);
                if(Carbon::now()->between($start, $end, true)) {
                    $radius = $time->radius;
                }
            }
        }
        
        return Driver::selectRaw("id, en_name, ar_name , lat, lon, email , mobile , photo, fcm_token,
                        ( 6371 * acos( cos( radians(?) ) *
                        cos( radians( lat ) )
                        * cos( radians( lon ) - radians(?)
                        ) + sin( radians(?) ) *
                        sin( radians( lat ) ) )
                        ) AS distance", [$latitude, $longitude, $latitude])
                ->when($user_id, function($query) use ($user_id){
                    return $query->whereHas('users', function($q) use ($user_id){
                        $q->where('user_id', $user_id);
                    });
                })
                ->where('status', '=', 1)
                ->where('isOnline', '=', 1)
                ->having("distance", "<", $radius)
                ->orderBy("distance",'asc')
                ->offset(0)
                ->limit(20);
    }
}

