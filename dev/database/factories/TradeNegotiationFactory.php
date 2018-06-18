<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Trade\TradeNegotiation::class, function (Faker $faker) {
    
    $marketNegotiation = factory(App\Models\Market\MarketNegotiation::class)->create();

    return [
			"user_market_id" => $marketNegotiation->user_market_id,
			"trade_negotiation_id" => null,
			"market_negotiation_id" => $marketNegotiation->id,
			"initiate_user_id" => factory(App\Models\UserManagement\User::class)->create()->id,
			"recieving_user_id" => factory(App\Models\UserManagement\User::class)->create()->id,
			"trade_negotiation_status_id" => factory(App\Models\Trade\TradeNegotiationStatus::class)->create()->id,
			"traded" => rand(0,1) == 1,
			"quantity" => rand(0,300),
			"is_offer" => rand(0,1) == 1,
			"is_distpute" => false
    ];
});