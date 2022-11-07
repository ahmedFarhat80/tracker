<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Resources\Admin\UserResource;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Shipping\BankInfoRequest;
use App\Http\Requests\Admin\ChangeAvatarRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Fare;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::selection()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return UserResource::collection($data);
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
    public function store(UserRequest $request)
    {
        try {
            $user = $this->process(new User, $request);

            $user->password            = $request->password;
            $user->status              = $request->status ? 1 : 0;
            $user->save();

            $fare = Fare::firstOrCreate([
                'user_id'     => $user->id,
                'base_fare'   => 1,
            ]);

            return $this->show($user->id);
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
            $data = User::selection()->findOrFail($id);
            return new UserResource($data);
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
    public function update(UserRequest $request)
    {
        try {

            if (\Auth::guard('admin-api')->check()) {
                $this->validate($request, [
                    'user_id'   => 'required',
                ]);

                $id       = $request->user_id;
            } elseif (\Auth::guard('user-api')->check()) {

                /* Check user request id is the same loged in id */
                if (Gate::denies('user-update-profile', $request))
                    return errorMessage('Forbidden Error: ID does not match loged-in user id!', 403);

                $id       = \Auth::guard('user-api')->id();
            }

            $data     = User::findorFail($id);
            $user     = $this->process($data, $request);
            return $this->show($user->id);
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
            $data = User::findOrFail($id);

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
            $data           = User::selection()->findorFail($id);

            /* Unlink old image from helper function call */
            !empty($data->photo) ? UnlinkImage($data->photo) : '';
            $data->photo    = null;

            $data->save();

            return new UserResource($data, 200);
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
    protected function process(User $user, Request $request)
    {
        $filePath           = null;
        if ($request->has('photo')) {
            /* Unlink Old Image  from helper function call*/
            !empty($user->photo) ? UnlinkImage($user->photo) : '';
            $filePath         = uploadImage('user', $request->photo);
        } else {
            $filePath         = $user->getRawOriginal('photo');
        }

        $user->quote_id            = $request->quote_id;
        $user->en_name             = $request->en_name;
        $user->ar_name             = $request->ar_name;
        $user->email               = $request->email;
        $user->mobile              = $request->mobile;
        $user->address             = $request->address;
        $user->lon                 = $request->lon;
        $user->lat                 = $request->lat;
        // $user->password            = $request->password;
        // $user->status              = $request->status ? 1 : 0;
        $user->api_token           = \Str::random(60);
        $user->photo               = $filePath;
        $user->account_name        = $request->account_name;
        $user->swift_code          = $request->swift_code;
        $user->iban                = $request->iban;
        $user->SubMerchUID         = $request->SubMerchUID;


        $user->save();

        return $user;
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
            $data = User::selection()->findOrFail($id);

            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status]);

            return new UserResource($data);
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
            $id     = \Auth::guard('user-api')->id();
            $data   = User::findorFail($id);

            if (\Hash::check($request->old_password, $data->password)) {
                $data->fill([
                    'password' => $request->new_password
                ])->save();

                return new UserResource($data);
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

    public function changeBankInfo(BankInfoRequest $request)
    {

        try {
            // $id     = \Auth::guard('user-api')->id();
            // $data   = User::selection()->findorFail($id);

            // $data->account_name = $request->account_name;
            // $data->swift_code   = $request->swift_code;
            // $data->iban         = $request->iban;
            // // $data->SubMerchUID  = $request->SubMerchUID;

            // $data->save();

            // return new UserResource($data);
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
            $id     = \Auth::guard('user-api')->id();
            $data   = User::selection()->findOrFail($id);

            if ($request->hasFile('photo')) {

                /* Unlink old image from helper function call */
                !empty($data->photo) ? UnlinkImage($data->photo) : '';

                $filePath       = uploadImage('user', $request->photo);
                User::where('id', $id)->update([
                    'photo'       => $filePath,
                ]);
            }

            return new UserResource($data);
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
        $search  = $request->search;
        $users   = User::selection()->where('en_name', 'like', '%' . $search . '%')
            ->orWhere('ar_name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('mobile', 'like', "%{$search}%")
            ->orWhere('address', 'like', "%{$search}%")
            ->orderBy('id')->paginate(PAGINATION_COUNT);

        // if($users->count() == 0)
        //     return noDataFound(__('admin.no_data'));

        return UserResource::collection($users);
    }

    public function showByToken()
    {
        $users = User::with('transactions')->findOrFail(\Auth::guard('user-api')->id());
        return response($users, 200);
    }
}
