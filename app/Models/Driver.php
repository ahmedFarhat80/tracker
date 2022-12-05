<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use DB;

class Driver extends Model implements AuthenticatableContract , JWTSubject
{
    use HasFactory, Notifiable;

    use Authenticatable;

    protected $table = 'drivers';

    protected $fillable = [
        'en_name', 'ar_name','email' ,'mobile','address', 'photo' , 'lon' , 'lat' ,'sequence', 'isOnline' ,'status' , 'password' , 'otp' ,'api_token' , 'fcm_token'  ,'created_at' , 'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'api_token', 'password' , 'otp' , 'fcm_token'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /* Make Muterator for img in model */
    public function getPhotoAttribute($val){
        return $val !== null ? asset('assets/'. $val) : asset('avatar.png');
    }

    /* Make hashing for password */
    public function setPasswordAttribute($value){
        $this->attributes['password'] = \Hash::make($value);
    }

    public function scopeActive($query){
        return $query->where('status' , 1);
    }

    public function scopeSelection($query){
        return $query->select('id' , app()->getLocale().'_name' . ' as name' , 'en_name' , 'ar_name' , 'email' , 'mobile' , 'lon' , 'lat' ,'photo' , 'status' , 'isOnline','address','created_at');
    }

    public function users(){
        return $this->belongsToMany('App\Models\User' , 'user_driver')
                    ->select('users.id as user_id','quote_id' , app()->getLocale().'_name' . ' as name' , 'email' , 'mobile' , 'lon' , 'lat' , 'address' , 'photo' , 'status')
                    ->withTimestamps();
    }

    public function orders(){
        return $this->hasMany('App\Models\Order');
    }

    public function acceptedOrders() {
        return $this->orders()->where('status','=', 'approved')
                    ->with('restaurant');
    }

    public function deliveredOrders() {
        return $this->orders()->where('status','=', 'delivered')
                    ->with('restaurant');
    }

    public function rejectRequests(){
        return $this->hasMany('App\Models\RejectRequest' , 'driver_id')
                    ->with('restaurant');
    }

    public static function filter($filter , $user_id = null){
        $driver = new Driver();
        $drivers = $driver->selection()->where(function ($query) use ($filter) {
            $query->where('en_name', 'like', "%{$filter}%")
                ->orWhere('ar_name', 'LIKE', "%{$filter}%")
                ->orWhere('email', 'LIKE', "%{$filter}%")
                ->orWhere('mobile', 'LIKE', "%{$filter}%");
        })
        ->when($user_id, function($q) use ($user_id){
            $q->whereHas('users', function($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        })->paginate(PAGINATION_COUNT);

	    return $drivers;
    }
}
