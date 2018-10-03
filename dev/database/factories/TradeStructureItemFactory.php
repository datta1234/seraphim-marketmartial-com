<?php

use Faker\Generator as Faker;

$factory->define(App\Models\StructureItems\Item::class, function (Faker $faker) {
   	return [
	   'title' => $faker->word,
        'item_type_id'  =>  function() {
            return factory(App\Models\StructureItems\ItemType::class)->create()->id;
        },
        'trade_structure_group_id'  =>  function() {
            return factory(App\Models\StructureItems\TradeStructureGroup::class)->create()->id;
        },
	];
});
