<?php

use Faker\Generator as Faker;
$factory->define(App\Models\Market\MarketNegotiationStatus::class, function (Faker $faker) {
    return [
        'title' => $faker->word
    ];
});
