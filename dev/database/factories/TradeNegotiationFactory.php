<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Trade\TradeNegotiation::class, function (Faker $faker) {
    return [
			"market_negotiation_id" => function() {
                return factory(App\Models\Market\MarketNegotiation::class)->create()->id;
            },
            "user_market_id" => function($tradeNegotiation) {
                return App\Models\Market\MarketNegotiation::find($tradeNegotiation['market_negotiation_id'])->user_market_id;
            },
            "trade_negotiation_id" => null,
			"initiate_user_id" => function(){
                return factory(App\Models\UserManagement\User::class)->create()->id;
            },
			"recieving_user_id" => function(){
                return factory(App\Models\UserManagement\User::class)->create()->id;
            },
			"trade_negotiation_status_id" => function(){
                return factory(App\Models\Trade\TradeNegotiationStatus::class)->create()->id;
            },
			"traded" => rand(0,1) == 1,
			"quantity" => rand(0,300),
			"is_offer" => rand(0,1) == 1,
			"is_distpute" => false
    ];
});