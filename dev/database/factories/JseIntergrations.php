<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ApiIntegration\JseIntergration::class, function (Faker $faker) {
    $types = ['string','number','boolean'];
    return [
		"type" => $types[array_rand($types)],
		"field" => $faker->word,
		"value" => $faker->word
    ];
});