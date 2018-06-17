<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\Interest::class, function (Faker $faker) {
    return [
        'title'=> $faker->word
    ];
});
