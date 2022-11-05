<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fare extends Model
{
    use HasFactory;

    protected $table = 'fares';

    protected $fillable = [
        'user_id','base_fare','created_at' , 'updated_at',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id');
    }
}
