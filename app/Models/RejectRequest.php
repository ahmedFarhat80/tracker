<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectRequest extends Model
{
    use HasFactory;

    protected $table = 'reject_requests';

    protected $fillable = [
        'driver_id', 'order_id','reason','created_at' , 'updated_at',
    ];

    public function driver(){
        return $this->belongsTo('App\Models\Driver' , 'driver_id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Order' , 'order_id')
                    ->with('restaurant');
    }
}
