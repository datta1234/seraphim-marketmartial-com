<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\DefaultLabel::class, function (Faker $faker) {
   return [
        "title" => $faker->word,
    ];
});
