<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name'              => 'Superadmin',
            'email'             => 'ahmed@gmail.com',
            'password'          => '12345678',
            'status'            => true,
            'api_token'         => \Str::random(60),
            // 'remember_token'    => str_random(10),
        ]);
    }
}
