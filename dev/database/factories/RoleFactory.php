<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\Role::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'is_selectable' => 1
    ];
});
