<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Market\UserMarketStatus::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
    ];
});
