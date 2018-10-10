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
        'trading_account_id' => function(){
            return factory(App\Models\UserManagement\TradingAccount::class)->create()->id;
        },
    ];
});