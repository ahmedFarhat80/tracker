<?php

namespace App\Http\Controllers\Api\Flutter\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\Flutter\Driver\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(){
        $notifications = \Auth::guard('driver-api')->user()->notifications()->paginate(PAGINATION_COUNT);
        return new NotificationResource($notifications);
    }

    // ======================================================================================================
    public function unRead(){
        $notifications = \Auth::guard('driver-api')->user()->unreadNotifications()->paginate(PAGINATION_COUNT);
        return new NotificationResource($notifications);
    }

    // ======================================================================================================
    public function makeRead($id){
        $notifications = \Auth::guard('driver-api')->user()
                                ->notifications
                                ->where('id' , $id)
                                ->markAsRead();
        return response([
            'status'    => 'success',
            'message'   => "updated success"]
            , 200);
    }

    // ======================================================================================================
    public function destroy($id){
        try{
            $data = \Auth::guard('driver-api')->user()
                                ->notifications
                                ->where('id' , $id)
                                ->first();
            
            abort_if(!$data, 403, 'No data found!.');
            
            $data = [
                'status'    => 'success',
                'message'   => __('dashboard.delete_success'),
            ];
            
            return response($data, 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
