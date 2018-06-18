<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TradeConfirmations\DistputeStatus::class, function (Faker $faker) {
    return [
        'title'	=> $faker->word
    ];
});
