<?php

use Faker\Generator as Faker;

$factory->define(App\Models\MarketRequest\UserMarketRequestGroup::class, function (Faker $faker) {
    return [
		"user_market_request_id" => function(){
            return factory(App\Models\MarketRequest\UserMarketRequest::class)->create()->id;
        },
		"trade_structure_group_id" => function(){
            return factory(App\Models\StructureItems\TradeStructure::class,'Outright')->create()->id;
        },
		"is_selected" => rand(0,1) == 1
    ];
});

