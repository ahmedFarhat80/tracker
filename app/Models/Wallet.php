<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $primarykey = 'id'; 

	protected $table = 'wallets';

    protected $fillable = [
        'user_id', 'restaurant_id', 'MerchantTxnRefNo' , 'budget', 'date' , 'status' ,'created_at' , 'updated_at'
    ];
}
