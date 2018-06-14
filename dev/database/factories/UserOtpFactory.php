<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\UserOtp::class, function (Faker $faker) {
    return [
        'otp' => $faker->numberBetween(100000, 999999),
    ];
});
