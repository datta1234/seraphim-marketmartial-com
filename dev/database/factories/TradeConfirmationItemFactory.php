<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TradeConfirmations\TradeConfirmationItem::class, function (Faker $faker) {
    return [
		"item_id"						=> function(){
			return factory(App\Models\StructureItems\Item::class)->create()->id;
		},
		"trade_confirmation_group_id"	=> function(){
            return factory(App\Models\TradeConfirmations\TradeConfirmationGroup::class)->create()->id;
		},
		"value"							=> $faker->word,
		"title"							=> $faker->word
    ];
});
