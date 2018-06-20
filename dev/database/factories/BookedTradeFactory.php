<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TradeConfirmations\BookedTrade::class, function (Faker $faker) {
    return [
		"user_id" => function(){
			$user =  factory(App\Models\UserManagement\User::class)->create();
			return $user->id;
		},
		"trade_confirmation_id" => null, 
		"trading_account_id" => function($bookedTrade) {
			return factory(App\Models\UserManagement\TradingAccount::class)->create(['user_id'=>$bookedTrade['user_id']])->id;
		},
		"market_id" =>  function(){
			return factory(App\Models\StructureItems\Market::class)->create()->id;
		},
		"stock_id" => function(){
			return factory(App\Models\StructureItems\Stock::class)->create()->id;
		},
		"booked_trade_status_id" => function(){
			return factory(App\Models\TradeConfirmations\BookedTradeStatus::class)->create()->id;
		},
		"is_sale" => rand(0,1) ==1,
		"is_confirmed" => rand(0,1) ==1,
		"is_rebate" => 0,
		"amount" => rand(0,1) == 1
	];
});
