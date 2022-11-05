<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(AdminSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(DriverTableSeeder::class);
        $this->call(RestaurantTableSeeder::class);
        $this->call(UserDriverTableSeeder::class);
        $this->call(OrderTableSeeder::class);
        $this->call(PaymentInfoTableSeeder::class);
    }
}
