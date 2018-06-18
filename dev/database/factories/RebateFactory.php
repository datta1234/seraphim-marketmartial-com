<?php

use Faker\Generator as Faker;
 
$factory->define(App\Models\Trade\Rebate::class, function (Faker $faker) {
    
    $userMarket = factory(App\Models\Market\UserMarket::class)->create();
    return [
		"user_id" => $userMarket->user_id,
		"user_market_id" => $userMarket->id,
		"organisation_id" => NULL,
		"user_market_request_id" => $userMarket->user_market_request_id,
		"booked_trade_id" => null,
		"is_paid" => rand(0,1) == 1,
		"trade_date" => $faker->date()
    ];
});
