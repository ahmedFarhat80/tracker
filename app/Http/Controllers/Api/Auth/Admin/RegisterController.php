<?php

namespace App\Http\Controllers\Api\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\Admin\RegisterRequest;
use App\Http\Resources\Auth\Admin\RegisterResource;
use Illuminate\Support\Str;
use App\Models\Admin;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try{
            $admin = Admin::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => $request->password,
                'api_token'     => Str::random(60),
            ]);
            
            $authToken = $admin->api_token; // createToken('authToken');
    
            return new RegisterResource($admin, $authToken);
        }
        catch(\Exception $ex){
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    	
    }
}
