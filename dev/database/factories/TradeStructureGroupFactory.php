<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\TradeStructureGroup::class, function (Faker $faker) {
       	return [
		   'title' => $faker->word,
           'trade_structure_id' => function() {
                return App\Models\StructureItems\TradeStructureGroup::all()->random()->id;    
           }
		];
});
