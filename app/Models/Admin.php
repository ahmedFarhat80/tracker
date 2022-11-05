<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;

class Admin extends Model implements AuthenticatableContract, JWTSubject
{
    use HasFactory, Notifiable;

    use Authenticatable;

    protected $primarykey = 'id';

    protected $table = 'admins';

    protected $fillable = [
        'name', 'email', 'password', 'photo', 'status', 'api_token', 'created_at', 'updated_at'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
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
    public function getPhotoAttribute($val)
    {
        return $val !== null ? asset('assets/' . $val) : '';
    }

    /* Make hashing for password */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSelection($query)
    {
        return $query->select('id', 'name', 'email', 'photo', 'status')
            ->where('id', '<>', 1)
            ->where('id', '<>', \Auth::guard('api')->user()->id);
    }
}
