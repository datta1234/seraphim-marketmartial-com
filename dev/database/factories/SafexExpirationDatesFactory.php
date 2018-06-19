<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\SafexExpirationDate::class, function (Faker $faker) {
    return [
        "expiration_date" => $faker->dateTime() 
    ];
});
