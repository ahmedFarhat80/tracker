<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForeignTransaction extends Model
{
    use HasFactory;

    protected $primarykey = 'id'; 

	protected $table = 'foreign_transactions';

    protected $fillable = [
        'user_id','MerchantTxnRefNo','amount' ,'name','email' , 'mobile' ,'date' , 'status','created_at' , 'updated_at',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User' , 'user_id');
    }
}
