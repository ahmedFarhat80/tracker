<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentInfo;

class PaymentInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentInfo::create([
            'MerchUID'      => 'mer21000381',
            'SubMerchUID'   => 'subm21000592',
            'account_name'  => 'test',
            'swift_code'    => 'ABC',
            'iban'          => '1234123412341234',
            'secretKey'     => '0123930',
        ]);
    }
}
