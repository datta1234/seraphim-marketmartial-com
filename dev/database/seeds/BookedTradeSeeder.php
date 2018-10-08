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
        $tradeConfirmations = App\Models\TradeConfirmations\TradeConfirmation::where('is_confirmed', true)->get();

        foreach ($tradeConfirmations as $tradeConfirmation) {
        	$sellingUser = $tradeConfirmation->send_user_id;
        	$buyingUser = $tradeConfirmation->receiving_user_id;
        	$marketMaker = $tradeConfirmation->tradeNegotiation->userMarket->user->id;


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
				"amount" => $tradeConfirmation->net_premiums,
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
				"amount" => $tradeConfirmation->net_premiums,
            ]);

        	if($tradeConfirmation->market_id !== 5) {
        		$marketMakerTradingAccount = App\Models\UserManagement\TradingAccount::where("user_id", $marketMaker)->where("market_id", $tradeConfirmation->market_id)->first()->id;

	        	// Create Sell rebate booked trade
	        	$sellRebateBookedTrade = factory(App\Models\TradeConfirmations\BookedTrade::class)->create([
					"user_id" => $marketMaker,
					"trade_confirmation_id" => $tradeConfirmation->id, 
					"trading_account_id" => $marketMakerTradingAccount,
					"market_id" => $tradeConfirmation->market_id,
					"stock_id" => $tradeConfirmation->stock_id,
					"is_sale" => 1,
					"is_confirmed" => 1,
					"is_rebate" => 1,
					"amount" => $tradeConfirmation->future_reference
	            ]);

	            // Create Rebate for Sell rebate booked trade
	        	$sellRebate = factory(App\Models\Trade\Rebate::class)->create([
					"user_id" => $sellRebateBookedTrade->user_id,
					"user_market_id" => $tradeConfirmation->tradeNegotiation->userMarket->id,
					"organisation_id" => $sellRebateBookedTrade->user->organisation->id,
					"user_market_request_id" => $tradeConfirmation->tradeNegotiation->userMarket->userMarketRequest->id,
					"booked_trade_id" => $sellRebateBookedTrade->id,
					"is_paid" => 1,
					"trade_date" => Carbon::now()->addMonths(rand(0,12)),
					
	            ]);

	            // Create Buy rebate booked trade
	        	$buyRebateBookedTrade = factory(App\Models\TradeConfirmations\BookedTrade::class)->create([
					"user_id" => $marketMaker,
					"trade_confirmation_id" => $tradeConfirmation->id, 
					"trading_account_id" => $marketMakerTradingAccount,
					"market_id" => $tradeConfirmation->market_id,
					"stock_id" => $tradeConfirmation->stock_id,
					"is_sale" => 0,
					"is_confirmed" => 1,
					"is_rebate" => 1,
					"amount" => $tradeConfirmation->future_reference - $tradeConfirmation->future_reference*0.25
	            ]);

	            // Create Rebate Buy rebate booked trade
	        	$buyRebate = factory(App\Models\Trade\Rebate::class)->create([
					"user_id" => $buyRebateBookedTrade->user_id,
					"user_market_id" => $tradeConfirmation->tradeNegotiation->userMarket->id,
					"organisation_id" => $buyRebateBookedTrade->user->organisation->id,
					"user_market_request_id" => $tradeConfirmation->tradeNegotiation->userMarket->userMarketRequest->id,
					"booked_trade_id" => $buyRebateBookedTrade->id,
					"is_paid" => 1,
					"trade_date" => Carbon::now()->addMonths(rand(0,12)),
	            ]);
        	}
        }
    }
}
