<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\Organisation::class, function (Faker $faker) {
    return [
		"title" => $faker->company,
		"verified" => rand(0,1) == 1,
		"description" => $faker->catchPhrase
    ];
});
