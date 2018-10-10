<?php

use Faker\Generator as Faker;
$factory->define(App\Models\StructureItems\TradeStructureGroupType::class,function (Faker $faker) {
    return [
	   'title' => $faker->word,
    ];
});
