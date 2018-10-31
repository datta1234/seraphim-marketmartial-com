<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class TradeNegotiationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$marketNegotiations = App\Models\Market\MarketNegotiation::where('is_accepted', true)->take(10)->get();


        foreach ($marketNegotiations as $marketNegotiation) {
        
                 $is_offer = rand(0,1) == 1;

                 $tradeNegotiation = factory(App\Models\Trade\TradeNegotiation::class)->create([
                   "market_negotiation_id" => $marketNegotiation->id,
                   "user_market_id" => $marketNegotiation->user_market_id,
                   "initiate_user_id" => $marketNegotiation->userMarket->userMarketRequest->user_id,
                   "recieving_user_id" => $marketNegotiation->user_id,
                   "traded" => true,
                   "quantity" => 500,//$is_offer ? $marketNegotiation->offer_qty : $marketNegotiation->bid_qty,
                   "is_offer" => $is_offer,
                   "is_distpute" => false,

               ])->each(function($tradeNegotiation){
                    $tradeNegotiation->setUpConfirmation();
               });
            
        }
    }
}
