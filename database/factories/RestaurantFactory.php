<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{

    /**

     * The name of the factory's corresponding model.

     *

     * @var string

     */

    protected $model = Restaurant::class;




    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'           => $this->faker->randomElement(\App\Models\User::all())['id'],
            'en_name'           => $this->faker->words(3 , true),
            'ar_name'           => $this->faker->words(3 , true),
            'email'             => $this->faker->unique()->safeEmail,
            'password'          => '12345678',
            'mobile'            => $this->faker->unique()->phoneNumber,
            'telephone'         => $this->faker->unique()->phoneNumber,
            'address'           => $this->faker->address,
            'lon'               => $this->faker->longitude(),
            'lat'               => $this->faker->latitude(),
            'api_token'         => Str::random(60),
            'status'            => true,
            'remember_token'    => Str::random(10),

        ];
    }
}
