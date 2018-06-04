<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\MarketType::class, function (Faker $faker) {
    return [
        "title" => $faker->word,
    ];
});
