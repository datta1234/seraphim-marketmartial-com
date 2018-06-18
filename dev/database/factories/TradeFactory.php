<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Trade\Trade::class, function (Faker $faker) {
    return [
        'trade_negotiation_id'	=> factory( App\Models\Trade\TradeNegotiation::class )->create()->id,
        'market_negotiation_id'	=> factory( App\Models\Market\MarketNegotiation::class )->create()->id,
        'user_market_id'		=> factory( App\Models\Market\UserMarket::class )->create()->id,
        'initiate_user_id'		=> factory( App\Models\UserManagement\User::class )->create()->id,
        'recieving_user_id'		=> factory( App\Models\UserManagement\User::class )->create()->id,
        'trade_status_id'		=> factory( App\Models\Trade\TradeStatus::class )->create()->id,
        'quantity'				=> $faker->randomFloat(2, 0, 9999 )

    ];
});
