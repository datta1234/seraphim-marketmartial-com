<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\UserNotificationType::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
    ];
});