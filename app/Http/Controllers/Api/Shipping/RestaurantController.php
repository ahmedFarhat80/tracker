<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\RestaurantRequest;
use App\Http\Resources\Admin\RestaurantResource;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\User;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data   = User::find(\Auth::guard('user-api')->id())->restaurants()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return UserResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RestaurantRequest $request)
    {
        try {
            $restaurant = $this->process(new Restaurant, $request);
            $restaurant->password = $request->password;
            $restaurant->save();

            return $this->show($restaurant->id);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RestaurantRequest $request, $id)
    {
        try {
            $data = Restaurant::findorFail($id);
            $restaurant = $this->process($data, $request);
            return $this->show($restaurant->id);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = Restaurant::selection()->findOrFail($id);
            return new RestaurantResource($data);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = Restaurant::selection()->findOrFail($id);

            $data->delete();

            $data = [
                'status'    => 'success',
                'message'   => __('admin.delete_success'),
            ];

            return response($data, 200);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAvatar()
    {

        try {
            $id             = \Auth::guard('user-api')->id();
            $data           = Restaurant::selection()->findorFail($id);

            /* Unlink old image from helper function call */
            !empty($data->photo) ? UnlinkImage($data->photo) : '';
            $data->photo    = null;

            $data->save();

            return new RestaurantResource($data, 200);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * store / update patients fields
     *
     * @param  User Object , Request
     * @return User
     */
    protected function process(Restaurant $restaurant, Request $request)
    {

        $filePath = null;
        if ($request->hasFile('photo')) {
            /* Unlink old image from helper function call */
            !empty($restaurant->photo) ? UnlinkImage($restaurant->photo) : '';
            $filePath       = uploadImage('restaurant', $request->photo);
        } else {
            $filePath       = $restaurant->getRawOriginal('photo');
        }


        $restaurant->user_id             = \Auth::guard('user-api')->id();
        $restaurant->quote_id            = $request->quote_id;
        $restaurant->en_name             = $request->en_name;
        $restaurant->ar_name             = $request->ar_name;
        $restaurant->email               = $request->email;
        $restaurant->mobile              = $request->mobile;
        $restaurant->telephone           = $request->telephone;
        $restaurant->note                = $request->note;
        $restaurant->address             = $request->address;
        $restaurant->lon                 = $request->lon;
        $restaurant->lat                 = $request->lat;
        $restaurant->status              = $request->status ? 1 : 0;
        $restaurant->api_token           = \Str::random(60);
        $restaurant->photo               = $filePath;

        $restaurant->save();

        return $restaurant;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function status(Request $request, $id)
    {
        try {
            /**
             * WILL DISCUSS
             * MUST BE CHECKED FIRST
             * IS USER HAS BEEN SELECTED QUOTE OR NOT BEFORE CHANGE STATUS INTO ACTIVE
             * **/
            $data = Restaurant::selection()->findOrFail($id);

            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status]);

            return new RestaurantResource($data);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display a filter listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $search         = $request->search;
        $user_id        = \Auth::guard('user-api')->id();
        $restaurants    = Restaurant::filter($search, $user_id);
        return RestaurantResource::collection($restaurants);
    }
}
