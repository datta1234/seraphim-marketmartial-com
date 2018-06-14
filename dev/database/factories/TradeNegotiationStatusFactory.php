<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Trade\TradeNegotiationStatus::class, function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});
