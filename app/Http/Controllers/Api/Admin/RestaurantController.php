<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Resources\Admin\RestaurantResource;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\ChangeAvatarRequest;
use App\Http\Requests\Admin\RestaurantRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Restaurant::selection()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return RestaurantResource::collection($data);
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
    public function update(RestaurantRequest $request)
    {
        try {

            /* Check restaurant request id is the same loged in id */
            // if (Gate::denies('restaurant-update-profile', $request))
            //     return errorMessage('Forbidden Error: ID does not match loged-in restaurant id!', 403);

            $id             = \Auth::guard('restaurant-api')->id();
            $data           = Restaurant::findorFail($id);
            $restaurant = $this->process($data, $request);
            return $this->show($restaurant->id);
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
            $data = Restaurant::findOrFail($id);

            $data->delete();

            $data = [
                'status'    => 'success',
                'message'   => __('dashboard.delete_success'),
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
        $filePath           = null;
        if ($request->has('photo')) {
            /* Unlink Old Image  from helper function call*/
            !empty($restaurant->photo) ? UnlinkImage($restaurant->photo) : '';
            $filePath         = uploadImage('restaurant', $request->photo);
        } else {
            $filePath         = $restaurant->getRawOriginal('photo');
        }

        // $restaurant->user_id             = $request->user_id;
        // $restaurant->quote_id            = $request->quote_id;
        $restaurant->en_name             = $request->en_name;
        $restaurant->ar_name             = $request->ar_name;
        $restaurant->email               = $request->email;
        $restaurant->mobile              = $request->mobile;
        $restaurant->telephone           = $request->telephone;
        $restaurant->address             = $request->address;
        $restaurant->note                = $request->note;
        $restaurant->lon                 = $request->lon;
        $restaurant->lat                 = $request->lat;
        // $restaurant->status              = $request->status ? 1 : 0;
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
            $data = Restaurant::findOrFail($id);

            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status]);

            return new RestaurantResource($data);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function changePassword(ChangePasswordRequest $request)
    {

        try {
            $id     = \Auth::guard('restaurant-api')->id();
            $data   = Restaurant::findorFail($id);

            if (\Hash::check($request->old_password, $data->password)) {
                $data->fill([
                    'password' => $request->new_password
                ])->save();

                return new RestaurantResource($data);
            } else {
                return errorMessage(__('dashboard.not_match'), 500);
            }
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function changeAvatar(ChangeAvatarRequest $request)
    {

        try {
            $id     = \Auth::guard('restaurant-api')->id();
            $data   = Restaurant::selection()->findOrFail($id);

            if ($request->hasFile('photo')) {

                /* Unlink old image from helper function call */
                !empty($data->photo) ? UnlinkImage($data->photo) : '';

                $filePath       = uploadImage('restaurant', $request->photo);
                Restaurant::where('id', $id)->update([
                    'photo'       => $filePath,
                ]);
            }

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
        $restaurants    = Restaurant::filter($search);
        return RestaurantResource::collection($restaurants);
    }

    public function showByToken()
    {
        $restaurant = Restaurant::findOrFail(\Auth::guard('restaurant-api')->id());
        return response($restaurant, 200);
    }
}
