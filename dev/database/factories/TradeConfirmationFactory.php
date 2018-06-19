<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TradeConfirmations\TradeConfirmation::class, function (Faker $faker) {

    return [
        'send_user_id'					=> factory(App\Models\UserManagement\User::class)->create()->id,
        'receiving_user_id'				=> factory(App\Models\UserManagement\User::class)->create()->id,
        'trade_id'						=> factory(App\Models\Trade\Trade::class)->create()->id,
		'spot_price'					=> ( rand(0,10) > 4 ) ? $faker->randomFloat(2, 1, 9999999 ) : NULL,
		'future_reference'				=> ( rand(0,10) > 4 ) ? $faker->randomFloat(2, 1, 9999999 ) : NULL,
		'near_expiery_reference'		=> ( rand(0,10) > 4 ) ? $faker->randomFloat(2, 1, 9999999 ) : NULL,
		'trade_confirmation_id'			=> NULL,
		'trade_confirmation_status_id'	=> factory( App\Models\TradeConfirmations\TradeConfirmationStatus::class )->create()->id,
		'market_id'						=> NULL,
		'stock_id'						=> NULL,
		'contracts'						=> $faker->randomFloat(2, 1, 9999999 ),
		'puts'							=> $faker->randomFloat(2, 1, 9999999 ),
		'calls'							=> $faker->randomFloat(2, 1, 9999999 ),
		'delta'							=> $faker->randomFloat(2, 1, 9999999 ),
		'gross_premiums'				=> $faker->randomFloat(2, 1, 9999999 ),
		'net_premiums'					=> $faker->randomFloat(2, 1, 9999999 ),
		'is_confirmed'					=> $faker->boolean(50),
		'trading_account_id'			=> factory(App\Models\UserManagement\TradingAccount::class)->create()->id
    ];

});
