<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Restaurant\DriverResource;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Driver;
use App\Models\User;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $id     = \Auth::guard('restaurant-api')->id();
        $data   = Restaurant::findorFail($id)->user->drivers()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return DriverResource::collection($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $restaurant = \Auth::guard('restaurant-api')->user();
            $user_id    = $restaurant->user_id;
            $data = Driver::whereHas('users' , function($q) use($user_id){
                $q->where('users.id' , $user_id);
            })
            ->selection()
            ->findOrFail($id);

            $data['all_distance']   = $data->orders->sum('distance');
            return new DriverResource($data);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display a filter listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request){
        $search  = $request->search;
        $user_id = \Auth::guard('restaurant-api')->user()->user->id;
        $drivers = Driver::filter($search , $user_id);
        return DriverResource::collection($drivers);
    }
}
