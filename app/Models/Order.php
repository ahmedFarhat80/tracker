<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'driver_id',
        'restaurant_id',
        'order_no',
        'client_name',
        'details',
        'price',
        'mobile',
        'address',
        'street',
        'flat',
        'floor',
        'building',
        'flat_type',
        'duration',
        'distance',
        'lon',
        'lat',
        'paid_status',
        'fare',
        'totalWithFare',
        'status',
        'created_at' ,
        'updated_at',
        'paid_cash',
        'origin_lat',
        'origin_lng',
        'origin_address',
        'receipt_type',
    ];

    public function scopeCurrentOrders($query){
        return $query->where('status' , 'approved');
    }

    public function driver(){
        return $this->belongsTo('App\Models\Driver')
                    ->selection();
    }

    public function restaurant(){
        return $this->belongsTo('App\Models\Restaurant')
                    ->with('user')
                    ->selection();
    }

    public function hasRestaurant($restaurant){
        return $this->restaurant()
            ->where('restaurant_id', $restaurant)
            ->exists();
    }

    public function rejecetd(){
        return $this->hasMany('App\Models\RejectRequest' , 'order_id');
    }
}
