<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DriverResource;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverController extends Controller
{
    /**
     * Display a filter listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request){
        $search  = $request->search;
        $user_id = \Auth::guard('user-api')->id();
        $drivers = Driver::filter($search , $user_id);
        return DriverResource::collection($drivers);
    }
}
