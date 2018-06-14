<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\Email::class, function (Faker $faker) {
    return [
		'title' => $faker->word,
		'email' => $faker->email,
		'default_id' => null,
		'notifiable'=> $faker->boolean(70)
    ];
});
