<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TradeConfirmations\TradeConfirmation::class, function (Faker $faker) {
    return [
        'send_user_id' => function(){
            return factory(App\Models\UserManagement\User::class)->create()->id;
        },
        'receiving_user_id' => function(){
            return factory(App\Models\UserManagement\User::class)->create()->id;
        },
        'trade_negotiation_id' => function(){
            return factory(App\Models\Trade\TradeNegotiation::class)->create()->id;
        },
        'trade_confirmation_status_id' => function(){
            return factory(App\Models\TradeConfirmations\TradeConfirmationStatus::class)->create()->id;
        },
        'trade_confirmation_id' => null,
        'stock_id' => null,
        'market_id' => function(){
            return factory(App\Models\StructureItems\Market::class)->create()->id;
        },
        'traiding_account_id' => function(){
            return factory(App\Models\UserManagement\TradingAccount::class)->create()->id;
        },
        'spot_price' => rand(1000,50000),
        'future_reference' => rand(1000,50000),
        'near_expiery_reference' => rand(0,3000),
        'contracts' => rand(500,3000),
        'puts' => rand(0,3000),
        'calls' => rand(0,3000),
        'delta' => rand(0,3000),
        'gross_premiums' => rand(0,3000),
        'net_premiums' => rand(0,3000),
        'is_confirmed' => rand(0,1) == 1,
    ];
});