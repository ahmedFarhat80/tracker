<?php

namespace App\Http\Controllers\Api\Admin;

use App\Interfaces\DriverRepositoryInterface;
use App\Http\Resources\Admin\DriverResource;
use App\Http\Resources\Admin\UserResource;
use App\Http\Requests\Admin\DriverRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\User;

class DriverController extends Controller
{
    private $driverRepository;

    public function __construct(DriverRepositoryInterface $driverRepository) 
    {
        $this->driverRepository = $driverRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        if(\Auth::guard('admin-api')->check()){
            $data   = $this->driverRepository->getAllDrivers();
            return DriverResource::collection($data);
        }elseif(\Auth::guard('user-api')->check()){
            $user_id  =\Auth::guard('user-api')->id();
            $data = Driver::selection()->whereHas('users', function($q) use($user_id) {
                $q->where('user_id', $user_id);
            })->paginate(PAGINATION_COUNT);
            return DriverResource::collection($data);
        }
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
    public function store(DriverRequest $request) 
    {
        try{
            $filePath = null;
            if ($request->hasFile('photo')) {
                $filePath  = uploadImage('driver', $request->photo);
            }

            $driverDetails = $request->only([
                'en_name',
                'ar_name',
                'email',
                'mobile',
                // 'password',
            ]);
            
            $driverDetails['photo']     = $filePath;
            $driverDetails['status']    = $request->status ? 1 : 0;
            $driverDetails['api_token'] = \Str::random(60);
            
            $driver = $this->driverRepository->createDriver($driverDetails);

            if(\Auth::guard('admin-api')->check()){
                $user   = User::find($request->user_ids);
            }elseif(\Auth::guard('user-api')->check()){
                $user   = User::find(\Auth::guard('user-api')->id());
            }

            $driver->users()->attach($user);
            
            return $this->show($driver->id);
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
    public function show($id)
    {
        /* IN-Case logged-in user */  
        if(\Auth::guard('user-api')->check()){
            /* Check driver specify logged-in user */  
            if (Gate::denies('user-driver-check', $id))
            return errorMessage('Forbidden Error: This driver does not represent the logged-in user', 403);
        }
        $data = $this->driverRepository->getDriverById($id);
        $data['all_distance']   = $data->orders->sum('distance');
        return new DriverResource($data);
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
    public function update(DriverRequest $request , $id) 
    {
        try{
            /* IN-Case logged-in user */  
            if(\Auth::guard('user-api')->check()){
                /* Check driver specify logged-in user */  
                if (Gate::denies('user-driver-check', $id))
                return errorMessage('Forbidden Error: This driver does not represent the logged-in user', 403);
            }

            $data = Driver::findOrFail($id);

            if ($request->hasFile('photo')) {
                /* Unlink old image from helper function call */
                !empty($data->photo) ? UnlinkImage($data->photo) : '';
                            
                $filePath       = uploadImage('driver', $request->photo);
            }else{
                $filePath       = $data->getRawOriginal('photo');
            }
    
            $driverDetails = $request->only([
                'en_name',
                'ar_name',
                'email',
                'mobile',
            ]);
            
            $driverDetails['photo']     = $filePath;
            $driverDetails['status']    = $request->status ? 1 : 0;
            
            $driver = $this->driverRepository->updateDriver($id, $driverDetails);

            if(\Auth::guard('admin-api')->check()){
                $user   = User::find($request->user_ids);
            }elseif(\Auth::guard('user-api')->check()){
                $user   = User::find(\Auth::guard('user-api')->id());
            }

            $driver->users()->sync($user);

            return $this->show($driver->id);
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
    public function destroy(Request $request , $id)
    {
        try{

            /* IN-Case logged-in user */  
            if(\Auth::guard('user-api')->check()){
                /* Check driver specify logged-in user */  
                if (Gate::denies('user-driver-check', $id))
                return errorMessage('Forbidden Error: This driver does not represent the logged-in user', 403);
            }

            $this->driverRepository->deleteDriver($id);

            $data = [
                'status'    => 'success',
                'message'   => __('driver.delete_success'),
            ];
            
            return response($data, 200);
            // return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getActiveDrivers() 
    {
        if(\Auth::guard('admin-api')->check()){
            $data   = $this->driverRepository->getActiveDrivers();
        }elseif(\Auth::guard('user-api')->check()){
            $data = User::whereHas('drivers', function ($query) {
                $query->where('status', 1);
           })->selection()->get();
        }
        return DriverResource::collection($data);
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
            /* IN-Case logged-in user */  
            if(\Auth::guard('user-api')->check()){
                /* Check driver specify logged-in user */  
                if (Gate::denies('user-driver-check', $id))
                return errorMessage('Forbidden Error: This driver does not represent the logged-in user', 403);
            }

            $data = Driver::findOrFail($id);
            
            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status ]);

            return new DriverResource($data);            

        } catch (\Exception $ex) {
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
        $drivers = Driver::filter($search);
        return DriverResource::collection($drivers);
    }
}
