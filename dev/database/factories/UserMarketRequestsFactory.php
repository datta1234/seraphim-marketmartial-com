<?php

use Faker\Generator as Faker;

$factory->define(App\Models\MarketRequest\UserMarketRequest::class, function (Faker $faker) {
	return [
		"user_id" =>  factory(App\Models\UserManagement\User::class)->create()->id,
		"trade_structure_id" => factory(App\Models\StructureItems\TradeStructure::class,'Outright')->create()->id,
		"user_market_request_statuses_id" => factory(App\Models\MarketRequest\UserMarketRequestStatus::class)->create()->id,
		"chosen_user_market_id" => null
	];
});
