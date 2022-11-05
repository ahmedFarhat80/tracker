<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'en_name'           => $this->faker->name,
            'ar_name'           => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'password'          => '12345678',
            'mobile'            => $this->faker->unique()->phoneNumber,
            'lon'               => $this->faker->longitude(),
            'lat'               => $this->faker->latitude(),
            'api_token'         => Str::random(60),
            'status'            => true,
        ];
    }
}
