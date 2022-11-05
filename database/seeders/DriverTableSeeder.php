<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DriverTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Driver::factory()->count(10)->create(); 
    }
}
