<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\FareRequest;
use Illuminate\Http\Request;
use App\Models\Fare;

class FareController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FareRequest $request){
        try{
            dd(\Auth::guard('user-api')->id());
            $fare = Fare::firstOrCreate([
                'user_id'     => \Auth::guard('user-api')->id(),
                'base_fare'   => $request->base_fare,
            ]);

            return response()->json([
                'status'   => "success",
                'data'     => $fare,
            ]);
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
    public function update(FareRequest $request, $id)
    {
        try{
            $user_id         = \Auth::guard('user-api')->id();
            $data            = Fare::where(['id' => $id , 'user_id' => $user_id])->first();
            $data->base_fare = $request->base_fare;
            $data->save();

            return response()->json([
                'status'   => "success",
                'data'     => $data,
            ]);
        } catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
