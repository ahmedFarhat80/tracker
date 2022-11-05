<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\FareRequest;
use Illuminate\Http\Request;
use App\Models\Fare;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FareController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FareRequest $request)
    {
        $validator = Validator($request->all(), [
            // 'user_id'        => 'required|numeric',
            'base_fare'        => 'required|numeric',
        ]);

        $id2 = 1;
        $fare = Fare::firstOrCreate([
            'user_id'     => $id2,
            'base_fare'   => $request->get('base_fare'),
        ]);
        if (!$validator->fails()) {

            return response()->json([
                'status'   => "success",
                'data'     => $fare,
            ]);
        } else {
            // return errorMessage($ex->getMessage(), 500);
            return response()->json(['message' => $validator->getMessageBag()->first(), 'Request' => Response::HTTP_BAD_REQUEST]);
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
        try {
            $user_id         = \Auth::guard('user-api')->id();
            $data            = Fare::where(['id' => $id, 'user_id' => $user_id])->first();
            $data->base_fare = $request->base_fare;
            $data->save();

            return response()->json([
                'status'   => "success",
                'data'     => $data,
            ]);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
