<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use DB;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        DB::table('orders')->delete();
	    $records = [
	        [
                'driver_id'         => 1,
                'restaurant_id'     => 1,
                'order_no'          => '#213',
                'client_name'       => 'Moamen',
                'mobile'            => '012341233322',
                'address'           => $this->faker->address,
                'lon'               => $this->faker->longitude(),
                'lat'               => $this->faker->latitude(),
                'price'             => '200',
                'details'           => '2 sandwitches compo',
            ],
            [
                'driver_id'         => 2,
                'restaurant_id'     => 1,
                'order_no'          => '#143',
                'client_name'       => 'ahmed',
                'mobile'            => '01213322',
                'address'           => $this->faker->address,
                'lon'               => $this->faker->longitude(),
                'lat'               => $this->faker->latitude(),
                'price'             => '140',
                'details'           => '1 sandwitches compo',  
            ],
            [
                'driver_id'         => 3,
                'restaurant_id'     => 1,
                'order_no'          => '#431',
                'client_name'       => 'mohamed',
                'mobile'            => '0123121111',
                'address'           => $this->faker->address,
                'lon'               => $this->faker->longitude(),
                'lat'               => $this->faker->latitude(),
                'price'             => '80',
                'details'           => '3 sandwitches',
            ],
            [
                'driver_id'         => 5,
                'restaurant_id'     => 2,
                'order_no'          => '#465',
                'client_name'       => 'sara',
                'mobile'            => '0109987623',
                'address'           => $this->faker->address,
                'lon'               => $this->faker->longitude(),
                'lat'               => $this->faker->latitude(),
                'price'             => '50',
                'details'           => '1 sandwitches compo',
            ],
            
	    ];

	    foreach ($records as $key => $record) {
	        DB::table('orders')->insert($record);
        }
    }
}
