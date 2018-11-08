<?php

use Faker\Generator as Faker;

$factory->define(App\Models\TradeConfirmations\BookedTrade::class, function (Faker $faker) {
   	$is_sale = rand(0,1) ==1;
   	$is_rebate = $is_sale ? false : rand(0,1) ==1;
    return [
		"user_id" => function(){
			return factory(App\Models\UserManagement\User::class)->create()->id;
		},
		"trade_confirmation_id" => function(){
			return factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create()->id;
		}, 
		"trading_account_id" => function($bookedTrade) {
			return factory(App\Models\UserManagement\TradingAccount::class)->create(['user_id'=>$bookedTrade['user_id']])->id;
		},
		"user_market_request_id" => function() {
			return factory(App\Models\MarketRequest\UserMarketRequest::class)->create()->id;
		},
		// "market_id" =>  function(){
		// 	return factory(App\Models\StructureItems\Market::class)->create()->id;
		// },
		// "stock_id" => function(){
		// 	return factory(App\Models\StructureItems\Stock::class)->create()->id;
		// },
		"is_sale" => $is_sale,
		"is_purchase" => !$is_sale && $is_rebate,

		"is_confirmed" => rand(0,1) ==1,
		"is_rebate" => $is_rebate,
		"amount" => rand(0,1) == 1,
	];
});
