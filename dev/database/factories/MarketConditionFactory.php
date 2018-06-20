<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Market\MarketCondition::class, function (Faker $faker) {
	return [
		'title' => $faker->word,
		'alias' => $faker->word,
		'market_condition_category_id' => function() {
            return factory(\App\Models\Market\MarketConditionCategory::class)->create([
					'title' =>  $faker->word
				])->id;
        }
	];
});
