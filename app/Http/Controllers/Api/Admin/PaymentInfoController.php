<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentInfoRequest;
use Illuminate\Http\Request;
use App\Models\PaymentInfo;

class PaymentInfoController extends Controller
{
    public function show(){
        $data               = PaymentInfo::findOrFail(1);
        return response()->json([
            'data'      => $data,
        ]);   
    }
    
    // =====================================================
    public function update(PaymentInfoRequest $request){
        $data               = PaymentInfo::findOrFail(1);
        $data->MerchUID     = $request->MerchUID;
        $data->SubMerchUID  = $request->SubMerchUID;
        $data->account_name = $request->account_name;
        $data->swift_code   = $request->swift_code;
        $data->iban         = $request->iban;
        $data->secretKey    = $request->secretKey;
        $data->save();

        return response()->json([
            'message'   => "success",
            'data'      => $data,
        ]);        
    }
}
