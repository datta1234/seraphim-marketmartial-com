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
					"is_distpute" => false
	            ]);

	    		$trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->initiateUser->id)
	    			->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
	    			->first();

                if($trading_account) {



    	            $tradeConfirmation = factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create([
    	                'send_user_id' => $tradeNegotiation->initiate_user_id,
    			        'receiving_user_id' => $tradeNegotiation->recieving_user_id,
    			        'trade_negotiation_id' => $tradeNegotiation->id,
    			        'stock_id' => null,
    			        'market_id' => $marketNegotiation->userMarket->userMarketRequest->market_id,
    			        'trading_account_id' => $trading_account->id,
    			        'is_confirmed' => true,
    			        'updated_at' => Carbon::now()->addMonths(rand(0,12)),
    	            ]);

                        $marketRequest = $marketNegotiation->userMarket-marketRequest;
                        $groups =  $marketRequest->tradeStructure->tradeStructureGroups()->where('trade_structure_group_type_id',3)->get();


                        foreach($groups as $tradeStructureGroup) {
                            
                            $tradeGroup = $marketRequest->userMarketRequestGroups()->create([
                                'trade_structure_group_id'  =>  $tradeStructureGroup->id,
                                'trade_confirmation_id'     =>  $tradeConfirmation->id,
                                "is_option"                 =>  $tradeStructureGroup->title == "Options Group" ? 1: 0,
                                'user_market_request_group_id' => $marketRequest->userMarketRequestGroups()->where('trade_structure_group_id',$tradeStructureGroup->trade_structure_group_id)->first()->id,
                            ]);


                            foreach($tradeStructureGroup->items as $item) {

                                $value = null;
                                switch ($item->title) {
                                    case 'is_offer':
                                        $value = $is_offer;
                                        break;
                                    case 'put':
                                        $value = rand(100,1000);
                                        break;
                                    case 'call':
                                        $value = rand(100,1000);
                                        break;
                                    case 'volatility':
                                        $value = $is_offer ? $marketNegotiation->offer :  $marketNegotiation->bid;
                                        break;
                                    case 'Gross Premiums':
                                       $value = rand(100,1000);
                                        break;
                                    case 'Net Premiums':
                                         $value = rand(100,1000);
                                        break;
                                }

                                factory(App\Models\TradeConfirmations\TradeConfirmationItem::class)->create([
                                    'item_id' => $item->id,
                                    'user_market_request_group_id' => $tradeGroup->id,
                                    'title' => $item->title,
                                    'value' =>  $value
                                ]);
                            }
                        }

                    //based


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
					"is_distpute" => false
	            ]);

	    		$trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->initiateUser->id)
	    			->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
	    			->first();

                if($trading_account) {
    	            $tradeConfirmation = factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create([
    	                'send_user_id' => $tradeNegotiation->initiate_user_id,
    			        'receiving_user_id' => $tradeNegotiation->recieving_user_id,
    			        'trade_negotiation_id' => $tradeNegotiation->id,
    			        'stock_id' => null,
    			        'market_id' => $marketNegotiation->userMarket->userMarketRequest->market_id,
    			        'trading_account_id' => $trading_account->id,
    			        'future_reference' => $marketNegotiation->future_reference,
    			        'contracts' => $tradeNegotiation->quantity,
    			        'is_confirmed' => true,
    			        'updated_at' => Carbon::now()->addMonths(rand(0,12)),
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
					"is_distpute" => false
	            ]);

	    		$trading_account = App\Models\UserManagement\TradingAccount::where('user_id',$tradeNegotiation->initiateUser->id)
	    			->where('market_id',$marketNegotiation->userMarket->userMarketRequest->market_id)
	    			->first();

                if($trading_account) {
    	            $tradeConfirmation = factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create([
    	                'send_user_id' => App\Models\UserManagement\User::inRandomOrder()->first()->id,
    			        'receiving_user_id' => App\Models\UserManagement\User::inRandomOrder()->first()->id,
    			        'trade_negotiation_id' => $tradeNegotiation->id,
    			        'stock_id' => null,
    			        'market_id' => $marketNegotiation->userMarket->userMarketRequest->market_id,
    			        'trading_account_id' => $trading_account->id,
    			        'future_reference' => $marketNegotiation->future_reference,
    			        'contracts' => $tradeNegotiation->quantity,
    			        'is_confirmed' => true,
    			        'updated_at' => Carbon::now()->addMonths(rand(0,12)),
    	            ]);
                }
    		}
    	}
    }
}
