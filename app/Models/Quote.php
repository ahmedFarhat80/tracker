<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $primarykey = 'id'; 

	protected $table = 'quotes';

    protected $fillable = [
        'en_title', 'ar_title', 'en_desc', 'ar_desc', 'sequence', 'cost' , 'drivers_count' , 'months' ,'status','created_at' , 'updated_at'
    ];

    public function scopeActive($query){
        return $query->where('status' , 1);
    }

    public function scopeSelection($query){
        return $query->select('id' , app()->getLocale().'_title' . ' as title' , 'en_title' , 'ar_title' , app()->getLocale().'_desc' . ' as desc' , 'en_desc' , 'ar_desc' , 'sequence' , 'months' ,'drivers_count' , 'cost' , 'status');
    }

    public function users(){
        return $this->hasMany('App\Models\User' , 'quote_id');
    }


    public function restaurants(){
        return $this->hasMany('App\Models\Restaurant' , 'quote_id');
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction' , 'quote_id');
    }

}
