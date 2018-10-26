<?php

use Faker\Generator as Faker;

$tradeConfirmations = collect(config('marketmartial.trade_confirmations_statuses'));
$factory->define(App\Models\TradeConfirmations\TradeConfirmation::class,function (Faker $faker) use($tradeConfirmations){
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
        'trade_confirmation_status_id' => function() {
            return factory(App\Models\TradeConfirmations\TradeConfirmationStatus::class)->create()->id;
        },
        'trade_confirmation_id' => null,
        'stock_id' => null,
        'market_id' => function(){
            return factory(App\Models\StructureItems\Market::class)->create()->id;
        },
        'receiving_trading_account_id' => null,
        'trade_confirmation_status_id' => null,
    ];
});
