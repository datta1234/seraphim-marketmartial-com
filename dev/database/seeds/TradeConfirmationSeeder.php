<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class TradeConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$marketNegotiations = App\Models\Market\MarketNegotiation::where('is_accepted', true)->get();
        $tradeConfirmationStatutes = App\Models\TradeConfirmations\TradeConfirmationStatus::where('id',4)->get();

    	foreach ($marketNegotiations as $marketNegotiation) {
    		$traded_count = rand(1,16);
    		for($i = 0; $i <= $traded_count; $i++) {
    			$is_offer = rand(0,1) == 1;
	    		
	    		$tradeNegotiation = factory(App\Models\Trade\TradeNegotiation::class)->create([
	                "market_negotiation_id" => $marketNegotiation->id,
		            "user_market_id" => $marketNegotiation->user_market_id,
					"initiate_user_id" => $marketNegotiation->userMarket->userMarketRequest->user_id,
					"recieving_user_id" => $marketNegotiation->user_id,
					"traded" => true,
					"quantity" => $is_offer ? $marketNegotiation->offer_qty : $marketNegotiation->bid_qty,
					"is_offer" => $is_offer,
					"is_dispute" => false,
             
	            ]);

	    		$send_trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->initiateUser->id)
	    			->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
	    			->first();
                $receiving_trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->recievingUser->id)
                    ->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
                    ->first();

                if($send_trading_account && $receiving_trading_account) {

                    $status_id = $tradeConfirmationStatutes->random()->id;
                    
    	            $tradeConfirmation = factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create([
    	                'send_user_id' => $tradeNegotiation->initiate_user_id,
    			        'receiving_user_id' => $tradeNegotiation->recieving_user_id,
    			        'trade_negotiation_id' => $tradeNegotiation->id,
    			        'stock_id' => null,
                        "market_id" =>  $marketNegotiation->userMarket->userMarketRequest->market_id,
                        "trade_structure_id" =>   $marketNegotiation->userMarket->userMarketRequest->trade_structure_id,
                        "user_market_request_id" =>  $marketNegotiation->userMarket->userMarketRequest->id,
    			        'send_trading_account_id' => $send_trading_account->id,
                        'receiving_trading_account_id' => $receiving_trading_account->id,
                        'trade_confirmation_status_id' => $status_id,
    			        'updated_at' => Carbon::now()->addMonths(rand(0,12))
    	            ]);

                       


                }

    		}

    		$traded_away_count = rand(1,16);
    		for($i = 0; $i <= $traded_away_count; $i++) {

    			$is_offer = rand(0,1) == 1;
	    		$init_user_id = App\Models\UserManagement\User::where('id','!=',$marketNegotiation->user_id)
	    			->where('id','!=',$marketNegotiation->userMarket->userMarketRequest->user_id)
	    			->inRandomOrder()
	    			->first();

	    		$tradeNegotiation = factory(App\Models\Trade\TradeNegotiation::class)->create([
	                "market_negotiation_id" => $marketNegotiation->id,
		            "user_market_id" => $marketNegotiation->user_market_id,
					"initiate_user_id" => $init_user_id,
					"recieving_user_id" => $marketNegotiation->user_id,
					"traded" => true,
					"quantity" => $is_offer ? $marketNegotiation->offer_qty : $marketNegotiation->bid_qty,
					"is_offer" => $is_offer,
					"is_dispute" => false
	            ]);

	    		$send_trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->initiateUser->id)
	    			->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
	    			->first();
                $receiving_trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->recievingUser->id)
                    ->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
                    ->first();

                if($send_trading_account && $receiving_trading_account) {
                   $val = $tradeConfirmationStatutes->random()->id;
                    

    	            $tradeConfirmation = factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create([
    	                'send_user_id' => $tradeNegotiation->initiate_user_id,
    			        'receiving_user_id' => $tradeNegotiation->recieving_user_id,
    			        'trade_negotiation_id' => $tradeNegotiation->id,
    			        'stock_id' => null,
                        "market_id" =>  $marketNegotiation->userMarket->userMarketRequest->market_id,
                        "trade_structure_id" =>   $marketNegotiation->userMarket->userMarketRequest->trade_structure_id,
                        "user_market_request_id" =>  $marketNegotiation->userMarket->userMarketRequest->id,
                        'send_trading_account_id' => $send_trading_account->id,
                        'receiving_trading_account_id' => $receiving_trading_account->id,
                        'trade_confirmation_status_id' => $status_id,
    			        'updated_at' => Carbon::now()->addMonths(rand(0,12))
    	            ]);
                }
    		}

    		$rand_traded_count = rand(1,16);
    		for($i = 0; $i <= $rand_traded_count; $i++) {
    			$is_offer = rand(0,1) == 1;
	    		
	    		$tradeNegotiation = factory(App\Models\Trade\TradeNegotiation::class)->create([
	                "market_negotiation_id" => $marketNegotiation->id,
		            "user_market_id" => $marketNegotiation->user_market_id,
					"initiate_user_id" => App\Models\UserManagement\User::inRandomOrder()->first()->id,
					"recieving_user_id" => App\Models\UserManagement\User::inRandomOrder()->first()->id,
					"traded" => true,
					"quantity" => $is_offer ? $marketNegotiation->offer_qty : $marketNegotiation->bid_qty,
					"is_offer" => $is_offer,
					"is_dispute" => false
	            ]);

	    		$send_trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->initiateUser->id)
	    			->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
	    			->first();
                $receiving_trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->recievingUser->id)
                    ->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
                    ->first();

                if($send_trading_account && $receiving_trading_account) {
                    $status_id = $tradeConfirmationStatutes->random()->id;
                    
    	            $tradeConfirmation = factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create([
    	                'send_user_id' => App\Models\UserManagement\User::inRandomOrder()->first()->id,
    			        'receiving_user_id' => App\Models\UserManagement\User::inRandomOrder()->first()->id,
    			        'trade_negotiation_id' => $tradeNegotiation->id,
    			        'stock_id' => null,
                        "market_id" =>  $marketNegotiation->userMarket->userMarketRequest->market_id,
                        "trade_structure_id" =>   $marketNegotiation->userMarket->userMarketRequest->trade_structure_id,
                        "user_market_request_id" =>  $marketNegotiation->userMarket->userMarketRequest->id,
                        'send_trading_account_id' => $send_trading_account->id,
                        'receiving_trading_account_id' => $receiving_trading_account->id,
                        'trade_confirmation_status_id' => $status_id,    			        
    			        'updated_at' => Carbon::now()->addMonths(rand(0,12))
    	            ]);
                }
    		}
    	}
    }
}
