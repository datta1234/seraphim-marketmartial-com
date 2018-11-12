<?php

use Faker\Generator as Faker;

$factory->define(App\Models\MarketRequest\UserMarketRequest::class, function (Faker $faker) {
	return [
		"user_id" =>  function(){
            return factory(App\Models\UserManagement\User::class)->create()->id;
        },
		"trade_structure_id" => function(){
            return factory(App\Models\StructureItems\TradeStructure::class,'Outright')->create()->id;
        },
		// "user_market_request_statuses_id" => function(){
  //           return factory(App\Models\MarketRequest\UserMarketRequestStatus::class)->create()->id;
  //       },
        "market_id" =>  function(){
            return factory(App\Models\StructureItems\Market::class)->create()->id;
        },
		"chosen_user_market_id" => null
	];
});
