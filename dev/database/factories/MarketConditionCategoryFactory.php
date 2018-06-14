<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Market\MarketConditionCategory::class, function (Faker $faker) {
    return [
        'title' => $faker->word
    ];
});
