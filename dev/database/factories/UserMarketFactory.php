<?php

use Faker\Generator as Faker;
$factory->define(App\Models\Market\UserMarket::class, function (Faker $faker) {
    return [
		'user_id'=> function(){
            return factory(App\Models\UserManagement\User::class)->create()->id;
        },
		'user_market_request_id' => function(){
            return factory(App\Models\MarketRequest\UserMarketRequest::class)->create()->id;
        },
		'user_market_status_id' => function(){
            return factory(App\Models\Market\UserMarketStatus::class)->create()->id;
        },
		'current_market_negotiation_id' => null,
		'is_trade_away' => false,
		'is_market_maker_notified' => false
    ];
});
