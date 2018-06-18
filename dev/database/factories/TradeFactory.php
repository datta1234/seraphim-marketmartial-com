<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Trade\Trade::class, function (Faker $faker) {
    return [
        'contracts'				=> randomFloat(2, 1, 99999999999 ),
        'trade_negotiation_id'	=> factory( App\Models\Trade\TradeNegotiation::class )->creat()->id,
        'market_negotiation_id'	=> factory( App\Models\Market\MarketNegotiation::class )->craete()->id,
        'user_market_id'		=> factory( App\Models\Market\UserMarket::class )->craete()->id,
        'initiate_user_id'		=> factory( App\Models\UserManagement\User::class )->create()->id,
        'recieving_user_id'		=> factory( App\Models\UserManagement\User::class )->create()->id,
        'trade_status_id'		=> factory( App\Models\Trade\TradeStatus::class )->create()->id,
        'quantity'				=> randomFloat(2, 1, 99999999999 )

    ];
});
