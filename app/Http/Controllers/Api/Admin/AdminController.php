<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Resources\Admin\AdminResource;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\ChangeAvatarRequest;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data = Admin::selection()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return AdminResource::collection($data);        
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
    public function store(AdminRequest $request){
        try{
            $admin = $this->process(new Admin , $request);
            if($admin){
                return new AdminResource($admin);
            }else{
                return errorMessage(__('admin.error'), 500);
            }
            return new AdminResource($admin);
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
            $data = Admin::where('id' , '<>' , 1)->findOrFail($id);
            return new AdminResource($data);
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
     * Update profile for admin
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminRequest $request){
        try{

            /* Check user request id is the same loged in id */  
            if (Gate::denies('admin-update-profile', $request))
                return errorMessage('Forbidden Error: ID does not match loged-in admin id!', 403);
            
            $id             = \Auth::guard('admin-api')->user()->id;
            $data           = Admin::findorFail($id);
            $filePath       = null;

            if ($request->hasFile('photo')) {

                /* Unlink old image from helper function call */
                !empty($data->photo) ? UnlinkImage($data->photo) : '';
                            
                $filePath       = uploadImage('admin', $request->photo);
            }else{
                $filePath         = $data->getRawOriginal('photo');
            }

            $data->name     = $request->name;
            $data->email    = $request->email;
            $data->photo    = $filePath;
            
            $data->save();

            return new AdminResource($data);

        }catch(\Exception $ex){
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
            $data = Admin::where('id' , '<>' , 1)
                        ->where('id' , '<>' , \Auth::guard('admin-api')->user()->id)
                        ->findOrFail($id);
            
            $data->delete();
            
            $data = [
                'status'    => 'success',
                'message'   => __('admin.delete_success'),
            ];
            
            return response($data, 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAvatar(){
        try{
            $id             = \Auth::guard('admin-api')->user()->id;
            $data           = Admin::findorFail($id);
            
            /* Unlink old image from helper function call */
            !empty($data->photo) ? UnlinkImage($data->photo) : '';
            $data->photo    = null;

            $data->save();
            
            return new AdminResource($data , 200);
            
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
    /**
     * store / update patients fields
     *
     * @param  admin Object , Request
     * @return admin
     */
    protected function process(Admin $admin , Request $request){
        
        $admin->name            = $request->name;
        $admin->email           = $request->email;
        $admin->password        = $request->password;
        $admin->status          = $request->status ? 1 : 0;
        
        $admin->save();

        return $admin;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function status(Request $request , $id)
    {
        try {

            $data = Admin::where('id' , '<>' , 1)
                        ->where('id' , '<>' , \Auth::guard('admin-api')->user()->id)
                        ->findOrFail($id);
            
            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status ]);

            return new AdminResource($data);            

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

    public function changePassword(ChangePasswordRequest $request){
        
        try{
            $id     = \Auth::guard('admin-api')->user()->id;
            $data   = Admin::findorFail($id);
            
            if (\Hash::check($request->old_password, $data->password)) { 
                $data->fill([
                    'password' => $request->new_password
                ])->save();

                return new AdminResource($data);
            }else{
                return errorMessage(__('admin.not_match'), 500);
            }

        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }
    
    /**
        * Update the specified resource in storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
    */

    public function changeAvatar(ChangeAvatarRequest $request){

        try{
            $id     = \Auth::guard('admin-api')->user()->id;
            $data   = Admin::findOrFail($id);

            if ($request->hasFile('photo')) {

                /* Unlink old image from helper function call */
                !empty($data->photo) ? UnlinkImage($data->photo) : '';
                            
                $filePath       = uploadImage('admin', $request->photo);
                Admin::where('id' , $id)->update([
                    'photo'       => $filePath,
                ]);
            }
            
            return new AdminResource($data);

        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display a filter listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request){
        $search  = $request->search;
        $admins  = Admin::where('name','like','%'.$search.'%')
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);

        // if($admins->count() == 0)
        //     return noDataFound(__('dashboard.no_data'));

        return AdminResource::collection($admins);
    }
}
