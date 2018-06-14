<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\Stock::class, function (Faker $faker) {
    return [
        'name'=> $faker->word,
        'code'=> substr($faker->word,0,2)
    ];
});
