<?php

use Faker\Generator as Faker;
 
$factory->define(App\Models\Trade\Rebate::class, function (Faker $faker) {
    return [
		"user_id" => function(){
			return factory(App\Models\UserManagement\User::class)->create()->id;
		},
		"user_market_id" => function(){
            return factory(App\Models\Market\UserMarket::class)->create()->id;
        },
		"organisation_id" => function(){
            return factory(App\Models\UserManagement\Organisation::class)->create()->id;
        },
		"user_market_request_id" => function(){
            return factory(App\Models\MarketRequest\UserMarketRequest::class)->create()->id;
        },
        "amount" => rand(1,100000),
		"is_paid" => rand(0,1) == 1,
		"trade_date" => $faker->date(),
		"booked_trade_id" => null
	];
});