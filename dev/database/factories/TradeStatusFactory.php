<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Trade\TradeStatus::class, function (Faker $faker) {
    return [
        'title' => $faker->word
    ];
});
