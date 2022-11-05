<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInfo extends Model
{
    use HasFactory;

    protected $primarykey = 'id'; 

	protected $table = 'payment_infos';

    protected $fillable = [
        'MerchUID', 'SubMerchUID', 'account_name', 'swift_code', 'iban', 'secretKey','created_at' , 'updated_at'
    ];
}
