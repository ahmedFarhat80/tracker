<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserDriverTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_driver')->delete();
	    $records = [
	        [
                'user_id'       => 1,
                'driver_id'     => 1,
            ],
            [
                'user_id'       => 1,
                'driver_id'     => 2,
            ],
            [
                'user_id'       => 1,
                'driver_id'     => 3,
            ],
            [
                'user_id'       => 1,
                'driver_id'     => 4,
            ],
            [
                'user_id'       => 2,
                'driver_id'     => 5,
            ],
            [
                'user_id'       => 2,
                'driver_id'     => 6,
            ],
            [
                'user_id'       => 2,
                'driver_id'     => 7,
            ],

            [
                'user_id'       => 3,
                'driver_id'     => 7,
            ],[
                'user_id'       => 3,
                'driver_id'     => 8,
            ],
            [
                'user_id'       => 3,
                'driver_id'     => 9,
            ],
            [
                'user_id'       => 3,
                'driver_id'     => 1,
            ],[
                'user_id'       => 4,
                'driver_id'     => 3,
            ],[
                'user_id'       => 4,
                'driver_id'     => 4,
            ],
	    ];

	    foreach ($records as $key => $record) {
	        // \App\Models\Page::create($record);
            DB::table('user_driver')->insert($record);
        }
    }
}
