<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookedTradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tradeConfirmations = App\Models\TradeConfirmations\TradeConfirmation::where('trade_confirmation_status_id', 4)->get();

        foreach ($tradeConfirmations as $tradeConfirmation) {
        	// When is offer is true the initiating user is the buying
        	$is_offer = $tradeConfirmation->tradeNegotiation->is_offer;
        	if($is_offer == 1) {
        		$sellingUser = $tradeConfirmation->receiving_user_id;
        		$buyingUser = $tradeConfirmation->send_user_id;
        	} else {
        		$sellingUser = $tradeConfirmation->send_user_id;
        		$buyingUser = $tradeConfirmation->receiving_user_id;
        	}
        	$marketMaker = $tradeConfirmation->tradeNegotiation->userMarket->user;

        	// Create Sell booked trade
        	$sellBookedTrade = factory(App\Models\TradeConfirmations\BookedTrade::class)->create([
				"user_id" => $sellingUser,
				"trade_confirmation_id" => $tradeConfirmation->id, 
				"trading_account_id" => App\Models\UserManagement\TradingAccount::where("user_id", $sellingUser)->where("market_id", $tradeConfirmation->market_id)->first()->id,
				"market_id" => $tradeConfirmation->market_id,
				"stock_id" => $tradeConfirmation->stock_id,
				"is_sale" => 1,
				"is_confirmed" => 1,
				"is_rebate" => 0,
				"amount" => rand(0,100)//$tradeConfirmation->net_premiums, @TODO get the amount from trade_confirmations
            ]);

            // Create Buy booked trade
        	$buyBookedTrade = factory(App\Models\TradeConfirmations\BookedTrade::class)->create([
				"user_id" => $buyingUser,
				"trade_confirmation_id" => $tradeConfirmation->id, 
				"trading_account_id" => App\Models\UserManagement\TradingAccount::where("user_id", $buyingUser)->where("market_id", $tradeConfirmation->market_id)->first()->id,
				"market_id" => $tradeConfirmation->market_id,
				"stock_id" => $tradeConfirmation->stock_id,
				"is_sale" => 0,
				"is_confirmed" => 1,
				"is_rebate" => 0,
				"amount" => rand(0,100)//$tradeConfirmation->net_premiums, @TODO get the amount from trade_confirmations
            ]);

        	if($tradeConfirmation->market_id !== 5) {
        		$market_make_trading_account = App\Models\UserManagement\TradingAccount::where("user_id", $marketMaker->id)->where("market_id", $tradeConfirmation->market_id)->first()->id;

        		$rebate = factory(App\Models\Trade\Rebate::class)->create([
					"user_id" => $marketMaker->id,
					"user_market_id" => $tradeConfirmation->tradeNegotiation->userMarket->id,
					"organisation_id" => $marketMaker->organisation->id,
					"user_market_request_id" => $tradeConfirmation->tradeNegotiation->userMarket->userMarketRequest->id,
					"is_paid" => 1,
					"trade_date" => Carbon::now()->addMonths(rand(0,12)),
	            ]);

	            // Create Buy rebate booked trade
	        	$buyRebateBookedTrade = factory(App\Models\TradeConfirmations\BookedTrade::class)->create([
					"user_id" => $marketMaker->id,
					"trade_confirmation_id" => $tradeConfirmation->id, 
					"trading_account_id" => $market_make_trading_account,
					"market_id" => $tradeConfirmation->market_id,
					"stock_id" => $tradeConfirmation->stock_id,
					"is_sale" => 0,
					"is_confirmed" => 1,
					"is_rebate" => 1,
					"amount" => rand(0,100),//$tradeConfirmation->future_reference,
					"rebate_trade_id" => $rebate->id
	            ]);

	        	// Create Sell rebate booked trade
	        	$sellRebateBookedTrade = factory(App\Models\TradeConfirmations\BookedTrade::class)->create([
					"user_id" => $marketMaker->id,
					"trade_confirmation_id" => $tradeConfirmation->id, 
					"trading_account_id" => $market_make_trading_account,
					"market_id" => $tradeConfirmation->market_id,
					"stock_id" => $tradeConfirmation->stock_id,
					"is_sale" => 1,
					"is_confirmed" => 1,
					"is_rebate" => 1,
					"amount" => rand(0,100),//$tradeConfirmation->future_reference - $tradeConfirmation->future_reference*0.25,
					"rebate_trade_id" => $rebate->id
	            ]);
        	}
        }
    }
}
