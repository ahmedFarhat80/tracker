<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

// class User extends Authenticatable implements JWTSubject , MustVerifyEmail
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primarykey = 'id';

	protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quote_id',
        'en_name',
        'ar_name',
        'email',
        'password',
        'photo',
        'mobile',
        'address',
        'account_name',
        'swift_code',
        'iban',
        'SubMerchUID',
        'lon',
        'lat',
        'status',
        'api_token' ,

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
        return $val !== null ? asset('assets/'. $val) : '';
    }

    /* Make hashing for password */
    public function setPasswordAttribute($value){
        $this->attributes['password'] = \Hash::make($value);
    }

    public function scopeActive($query){
        return $query->where('status' , 1);
    }

    public function scopeSelection($query){
        return $query->select('id','quote_id' , app()->getLocale().'_name' . ' as name', 'en_name' , 'ar_name' , 'email' , 'mobile', 'photo' , 'address' , 'lon' , 'lat' , 'account_name' , 'swift_code' , 'iban' , 'SubMerchUID' ,'status','created_at');
    }

    public function drivers(){
        return $this->belongsToMany('App\Models\Driver' , 'user_driver' , 'user_id')
                    ->select('drivers.id as driver_id' , app()->getLocale().'_name' . ' as name' , 'email' , 'mobile' , 'lon' , 'lat' , 'photo' , 'status' , 'isOnline')
                    ->withTimestamps();
    }

    public function quote(){
        return $this->belongsTo('App\Models\Quote');
    }

    public function restaurants(){
        return $this->hasMany('App\Models\Restaurant' , 'user_id')
                    ->selection()
                    ->with('orders');
    }

    public function hasRestaurant($restaurant) {
        return $this->restaurants->contains($restaurant);
    }

    public function hasSetting($setting) {
        return $this->settings->contains($setting);
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction' , 'user_id');
    }

    public function settings(){
        return $this->hasMany('App\Models\Setting' , 'user_id');
    }

    public function fare(){
        return $this->hasOne('App\Models\Fare' , 'user_id');
    }

    public function foreignTransaction(){
        return $this->hasMany('App\Models\ForeignTransaction' , 'user_id');
    }
}
