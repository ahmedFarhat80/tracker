<?php

namespace App\Http\Controllers\Api\Flutter\Driver;
use App\Http\Resources\Admin\DriverResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CodeService;
use App\Models\Driver;

class CheckPhoneController extends Controller
{

    public function phone(Request $request){
        
        $this->validate($request,[
            'mobile' =>  'required|numeric|digits:8',            
        ]);

        $driver = Driver::where('mobile' , $request->mobile)->first();
        if(empty($driver))
            return errorMessage("This phone was not found!", 500);

        $code             = rand(100000,999999);
        $message          = "verification code are ". $code;
        CodeService::send($driver->mobile , $message);
        $driver->otp      = $code;
        $driver->password = $code;
        $driver->save();

        return new DriverResource($driver);
    }
    
}
