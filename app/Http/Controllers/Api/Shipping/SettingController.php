<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shipping\SettingResource;
use App\Http\Requests\Shipping\SettingRequest;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $user_id  = \Auth::guard('user-api')->id();
        $data = Setting::where('user_id' , $user_id)->paginate(PAGINATION_COUNT);
        return SettingResource::collection($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingRequest $request){
        try{
            $setting = $this->process(new Setting , $request);
            return $this->show($setting->id);
        } catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $user        = \Auth::guard('user-api')->user();
            // check relation between user & restaurant
            if(!$user->hasSetting($id))
                return errorMessage("This setting not have relation with logged shipping!", 500);

            $data = Setting::findOrFail($id);
            return new SettingResource($data);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request, $id){
        try{
            $user        = \Auth::guard('user-api')->user();
            // check relation between user & restaurant
            if(!$user->hasSetting($id))
                return errorMessage("This setting not have relation with logged shipping!", 500);


            $data = Setting::findOrFail($id);
            $setting = $this->process($data , $request);
            return $this->show($setting->id);
        } catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            $user        = \Auth::guard('user-api')->user();
            // check relation between user & restaurant
            if(!$user->hasSetting($id))
                return errorMessage("This setting not have relation with logged shipping!", 500);

            $data = Setting::findOrFail($id);

            $data->delete();

            $data = [
                'status'    => 'success',
                'message'   => __('dashboard.delete_success'),
            ];

            return response($data, 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * store / update patients fields
     *
     * @param  Setting Object , Request
     * @return Setting
     */
    protected function process(Setting $setting , Request $request){
        $setting = Setting::firstOrCreate([
            'user_id'            => \Auth::guard('user-api')->id(),
            'time_from'          => date('h:i:s', strtotime($request->time_from)),
            'time_to'            => date('h:i:s', strtotime($request->time_to)),
            'radius'             => $request->radius,
            'distance_type'      => $request->distance_type,
        ]);

        return $setting;
    }
}
