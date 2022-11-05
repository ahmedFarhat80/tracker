<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'en_name'           => $this->faker->words(3 , true),
            'ar_name'           => $this->faker->words(3 , true),
            'email'             => $this->faker->unique()->safeEmail,
            'password'          => '12345678',
            'mobile'            => $this->faker->unique()->phoneNumber,
            'address'           => $this->faker->address,
            'lon'               => $this->faker->longitude(),
            'lat'               => $this->faker->latitude(),
            'api_token'         => Str::random(60),
            'status'            => true,
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
