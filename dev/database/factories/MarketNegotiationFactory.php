<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Market\MarketNegotiation::class, function (Faker $faker) {
    $hasPremium = rand(0,1) == 1;
    $hasBid = rand(0,1) == 1;
    $hasOffer = rand(0,1) == 1;
    return [
		"user_id" => function(){
            return factory(App\Models\UserManagement\User::class)->create()->id;
        },
		"market_negotiation_id" => null,
		"user_market_id" => function(){
            return factory(App\Models\Market\UserMarket::class)->create()->id;
        },
		"market_negotiation_status_id" => function(){
            return factory(App\Models\Market\MarketNegotiationStatus::class)->create()->id;
        },
		"bid" => $hasBid ? null:rand(0,10),
		"offer" => $hasOffer ? null:rand(0,10),
		"bid_qty" => $hasBid ? null:rand(0,3000),
		"offer_qty" => $hasOffer ? null:rand(0,3000),
		"bid_premium" => $hasPremium ? null : rand(0,3000),
		"offer_premium" => $hasPremium ? null : rand(0,3000),
		"future_reference" => $hasPremium ? null : rand(0,3000),
		"has_premium_calc" => $hasPremium,
		"is_repeat" => false,
		"is_accepted" => false
    ];
});
