<?php

use Faker\Generator as Faker;

$factory->define(App\Models\MarketRequest\UserMarketRequestTradable::class, function (Faker $faker) {
    $isMarket = rand(0,1) ==1;
    return [
			"user_market_request_id" => function(){
                return factory(App\Models\MarketRequest\UserMarketRequest::class)->create()->id;
            },
			"market_id" => function(){
                return $isMarket ? factory(App\Models\StructureItems\Market::class)->create()->id : null;
            },
			"stock_id" => function(){
                return !$isMarket ? factory(App\Models\StructureItems\Stock::class)->create()->id : null;
            }
    ];
});
