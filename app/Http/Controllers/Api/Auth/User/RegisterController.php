<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\User\RegisterRequest;
use App\Http\Resources\Auth\User\RegisterResource;
use Illuminate\Support\Str;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try{
            $user = User::create([
                'quote_id'          => $request->quote_id,
                'en_name'           => $request->en_name,
                'ar_name'           => $request->ar_name,
                'email'             => $request->email,
                'mobile'            => $request->mobile,
                'password'          => $request->password,
                'address'           => $request->address,
                'lon'               => $request->lon,
                'lat'               => $request->lat,
                'api_token'         => Str::random(60),
            ]);
            
            $authToken = $user->api_token; // createToken('authToken');
    
            return new RegisterResource($user, $authToken);
        }
        catch(\Exception $ex){
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    	
    }
}
