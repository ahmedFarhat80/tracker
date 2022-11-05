<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GeneratePasswordRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CodeService;
use App\Models\Restaurant;
use App\Models\User;

class GeneratePasswordController extends Controller
{
    public function generatePassword(GeneratePasswordRequest $request){
        try{
            $pwd = Str::random(8);

            if($request->type == 'user'){
                $data = User::where(['mobile' => $request->mobile , 'status' => 1])->first();
                abort_if(!$data, 403, 'Invalid mobile number!.');
                $data->password = $pwd;
                $data->save();
            }elseif($request->type == 'restaurant'){
                $data = Restaurant::where(['mobile' => $request->mobile , 'status' => 1])->first();
                abort_if(!$data, 403, 'Invalid mobile number!.');
                $data->password = $pwd;
                $data->save();
            }
    
            // send message
            $message    = "your password are ". $pwd;
            CodeService::send($data->mobile , $message);
            return response(["message" => "Message send successfully"], 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
