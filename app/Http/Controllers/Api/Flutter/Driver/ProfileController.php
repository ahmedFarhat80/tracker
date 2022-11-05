<?php

namespace App\Http\Controllers\Api\Flutter\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Flutter\Driver\FcmTokenRequest;
use App\Http\Requests\Flutter\Driver\LocationRequest;
use App\Http\Requests\Admin\ChangeAvatarRequest;
use App\Http\Resources\Admin\DriverResource;
use App\Services\GetPendingOrdersService;
use Illuminate\Http\Request;
use App\Models\RejectRequest;
use App\Models\Driver;
use App\Models\Order;
use Auth;

class ProfileController extends Controller
{
    public function info(){
        $driver = Driver::selection()->findOrFail(Auth::guard('driver-api')->id());
        $driver->sumDistance = $driver->orders->where('status' , 'delivered')->sum('distance');
        $driver->sumDuration = $driver->orders->where('status' , 'delivered')->sum('duration');
        return new DriverResource($driver);
    }
    // =========================================================================
    public function updateToken(FcmTokenRequest $request){
        try{
            // $driver = Auth::guard('driver-api')->user()->update(['fcm_token'=>$request->fcm_token]);
            $driver               = Driver::findOrFail(Auth::guard('driver-api')->id());
            $driver->fcm_token    = $request->fcm_token;
            $driver->save();
            return new DriverResource($driver);
        }catch(\Exception $ex){
            report($ex);
            return errorMessage($ex->getMessage(), 500);
        }
    }

    // =========================================================================
    public function updateLocation(LocationRequest $request){
        try{
            $driver         = Driver::findOrFail(Auth::guard('driver-api')->id());
            $driver->lon    = $request->lon;
            $driver->lat    = $request->lat;
            
            $driver->save();
            return new DriverResource($driver);
        }catch(\Exception $ex){
            report($ex);
            return errorMessage($ex->getMessage(), 500);
        }
    }
    
    // =========================================================================
    public function online(){
        try{
            $driver             = Driver::findOrFail(Auth::guard('driver-api')->id());
            $driver->isOnline   = ($driver->isOnline == 1) ? 0 : 1 ;
            $driver->save();
            return new DriverResource($driver);
        }catch(\Exception $ex){
            report($ex);
            return errorMessage($ex->getMessage(), 500);
        }
    }
    // =========================================================================
    public function pendingOrders(){
        try{
            $driver         = \Auth::guard('driver-api')->user();
            $longitude      = $driver->lon ; //31.377033;
            $latitude       = $driver->lat; //30.016893;
            $radius         = 100; // far KM "6371"
            $users_id       = $driver->users->pluck('user_id');

            if($driver->acceptedOrders->count() > 0){
                $orders = [];
            }else{
                $orders = GetPendingOrdersService::fetch(
                    $longitude , $latitude , $radius , $users_id
                )->get();
            }
            return response()->json(['pendingOrders' => $orders]);
        }catch(\Exception $ex){
            report($ex);
            return errorMessage($ex->getMessage(), 500);
        }
    }

    // =========================================================================
    public function acceptedOrders(){
        try{
            $driver = Driver::with('acceptedOrders')->findOrFail(Auth::guard('driver-api')->id());
            return response()->json(['acceptedOrders' => $driver->acceptedOrders]);
        }catch(\Exception $ex){
            report($ex);
            return errorMessage($ex->getMessage(), 500);
        }
    }

    // =========================================================================
    public function rejectedOrders(){
        try{
            $rejected = RejectRequest::with('driver' , 'order')->where('driver_id' , Auth::guard('driver-api')->id())->get();
            // $driver = Driver::with('rejectRequests')->findOrFail(Auth::guard('driver-api')->id());
            // return $driver;
            return response()->json(['rejectRequests' => $rejected]);
            // return new DriverResource($driver);
        }catch(\Exception $ex){
            report($ex);
            return errorMessage($ex->getMessage(), 500);
        }
    }

    // =========================================================================
    public function deliveredOrders(){
        try{
            $driver = Driver::with('deliveredOrders')->findOrFail(Auth::guard('driver-api')->id());
            // return $driver;
            return response()->json(['deliveredOrders' => $driver->deliveredOrders]);
            // return new DriverResource($driver);
        }catch(\Exception $ex){
            report($ex);
            return errorMessage($ex->getMessage(), 500);
        }
    }
    
    // =========================================================================
    public function changeAvatar(ChangeAvatarRequest $request){

        try{
            $driver     = \Auth::guard('driver-api')->user();

            if ($request->hasFile('photo')) {
                /* Unlink old image from helper function call */
                !empty($driver->photo) ? UnlinkImage($driver->photo) : '';
                            
                $filePath      = uploadImage('driver', $request->photo);
                $driver->photo = $filePath;
                $driver->save();
            }
            
            return new DriverResource($driver);

        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    // =========================================================================
    public function destroyAvatar(){
        try{
            $driver         = \Auth::guard('driver-api')->user();

            /* Unlink old image from helper function call */
            !empty($driver->photo) ? UnlinkImage($driver->photo) : '';
            $driver->photo  = null;
            $driver->save();
            
            return new DriverResource($driver , 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
