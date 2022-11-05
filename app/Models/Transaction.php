<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'quote_id', 'user_id','MerchantTxnRefNo','paymentCase' ,'num','date' , 'status','created_at' , 'updated_at',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function quote(){
        return $this->belongsTo('App\Models\Quote');
    }
}
