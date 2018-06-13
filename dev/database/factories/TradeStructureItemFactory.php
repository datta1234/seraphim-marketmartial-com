<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\Item::class, function (Faker $faker) {
   	return [
		   'title' => $faker->word,
		];
});
