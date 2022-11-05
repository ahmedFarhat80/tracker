<?php

namespace App\Http\Controllers\Api\Auth\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Restaurant\RegisterRequest;
use App\Http\Resources\Auth\Restaurant\RegisterResource;
use Illuminate\Support\Str;
use App\Models\Restaurant;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try{
            $user = Restaurant::create([
                'user_id'           => $request->user_id,
                'quote_id'          => $request->quote_id,
                'en_name'           => $request->en_name,
                'ar_name'           => $request->ar_name,
                'email'             => $request->email,
                'mobile'            => $request->mobile,
                'telephone'         => $request->telephone,
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
