<?php

use Faker\Generator as Faker;

$rules = [
	'required|date|after:today',
	'required|numeric'
];

$factory->define(App\Models\StructureItems\ItemType::class, function (Faker $faker) use ($rules) {
    $rulekey = array_rand($rules);
    return [
        'title' => $faker->name,
        'validation_rule' => $rules[$rulekey] 
    ];
});
