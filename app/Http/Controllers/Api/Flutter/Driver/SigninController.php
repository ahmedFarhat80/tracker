<?php

namespace App\Http\Controllers\Api\Flutter\Driver;


use App\Http\Resources\Flutter\Driver\SigninResource;
use App\Http\Requests\Flutter\Driver\SigninRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use JWTAuth;

class SigninController extends Controller
{
    public function login(SigninRequest $request){
        try{
            $mobile     = $request->mobile;
            $password   = $request->password;
            
            if (!$token = auth('driver-api')->attempt(['mobile' => $mobile, 'password' => $password, 'status'=>1])) {
                return errorMessage('Unauthenticated', 403);
            }

            $driver = auth('driver-api')->user();
            
            // Make online
            $driver->isOnline = 1;
            $driver->save();
            
            $driver->token = $token;

            return new SigninResource($driver, $token);

        }catch(\JWTException $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    public function logout (Request $request) {
        JWTAuth::invalidate(JWTAuth::getToken()); // LOGOUT
        $response = ['message' => __('dashboard.logout_success')];
        return response($response, 200);
    }
}
