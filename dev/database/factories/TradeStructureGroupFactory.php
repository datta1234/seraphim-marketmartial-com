<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\TradeStructureGroup::class, function (Faker $faker) {
       	return [
		   'title' => $faker->word,
		];
});
