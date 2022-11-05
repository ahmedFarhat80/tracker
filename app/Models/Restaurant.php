<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;

class Restaurant extends Model implements AuthenticatableContract , JWTSubject
{

    use HasFactory, Notifiable;

    use Authenticatable;

    protected $primarykey = 'id';

	protected $table = 'restaurants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'quote_id',
        'en_name',
        'ar_name',
        'email',
        'password',
        'photo',
        'mobile',
        'telephone',
        'address',
        'note',
        'lon',
        'lat',
        'wallet',
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
        return $query->select('id' , 'user_id','quote_id' , app()->getLocale().'_name' . ' as name', 'en_name' , 'ar_name' , 'email' , 'mobile', 'telephone', 'photo' , 'address' , 'note' , 'lon' , 'lat' , 'wallet' ,'status');
    }

    public function quote(){
        return $this->belongsTo('App\Models\Quote');
    }

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id')
                    ->selection()
                    ->with(['drivers' , 'fare']);
    }

    public function orders(){
        return $this->hasMany('App\Models\Order');
    }

    public static function filter($filter , $user_id = null){
        $restaurant = new Restaurant();
        $restaurants = $restaurant->selection()->where(function ($query) use ($filter) {
            $query->where('en_name', 'like', "%{$filter}%")
                ->orWhere('ar_name', 'LIKE', "%{$filter}%")
                ->orWhere('email', 'LIKE', "%{$filter}%")
                ->orWhere('mobile', 'LIKE', "%{$filter}%")
                ->orWhere('address', 'like', "%{$filter}%")
                ->orWhere('telephone', 'like', "%{$filter}%");
        })
        ->when($user_id, function($q) use ($user_id){
            $q->whereHas('user', function($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        })->paginate(PAGINATION_COUNT);

	    return $restaurants;
    }
}
