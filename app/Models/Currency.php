<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

	protected $table = 'currencies';

    protected $fillable = [
        'en_title', 'ar_title','exchange_rate' ,'symbol','sequence', 'status' ,'created_at' , 'updated_at',
    ];

    public function scopeActive($query){
        return $query->where('status' , 1);
    }

    public function scopeSelection($query){
        return $query->select('id' , app()->getLocale().'_title' . ' as title','en_title' , 'ar_title' , 'sequence' , 'symbol' , 'exchange_rate' , 'status');
    }
}
