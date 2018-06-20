<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\Market::class, function (Faker $faker) {
    return [
		"title" => $faker->word,
		"description" => $faker->sentence,
		"is_seldom" =>  rand(0,1) == 1,
		"has_deadline" => rand(0,1) == 1,
		"needs_spot" => rand(0,1) == 1,
		"has_negotiation" => rand(0,1) == 1,
		"has_rebate" => rand(0,1) == 1,
		"market_type_id" => function(){
			return factory(App\Models\StructureItems\MarketType::class)->create()->id;
		},
		"parent_id"	=> null,
		"is_displayed"	=> true,
		"is_selectable" => rand(0,1) == 1
    ];
});
