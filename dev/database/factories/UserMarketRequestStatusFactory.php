<?php

use Faker\Generator as Faker;

$factory->define(App\Models\MarketRequest\UserMarketRequestStatus::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
    ];
});
