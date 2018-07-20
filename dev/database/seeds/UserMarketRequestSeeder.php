<?php

use Illuminate\Database\Seeder;

class UserMarketRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $TradeStructures = App\Models\StructureItems\TradeStructure::all()->keyBy('title');

        if(!$TradeStructures['Outright']) {
            $TradeStructures['Outright'] = factory(App\Models\StructureItems\TradeStructure::class,'Outright')->create();
        }
        if(!$TradeStructures['Risky']) {
            $TradeStructures['Risky'] = factory(App\Models\StructureItems\TradeStructure::class,'Risky')->create();
        }
        if(!$TradeStructures['Calendar']) {
            $TradeStructures['Calendar'] = factory(App\Models\StructureItems\TradeStructure::class,'Calendar')->create();
        }
        if(!$TradeStructures['Fly']) {
            $TradeStructures['Fly'] = factory(App\Models\StructureItems\TradeStructure::class,'Fly')->create();
        }

        $markets = App\Models\StructureItems\Market::all();
        foreach($markets as $market) {

            if($market->marketType->tradeStructures)
            foreach($market->marketType->tradeStructures as $tradeStruct) {



                // factory(App\Models\MarketRequest\UserMarketRequest::class)->create([
                //     "market_id" =>  $market->id,
                //     "trade_structure_id" => $tradeStruct->id
                // ]);

                //for outright make some that are on hold
                if($tradeStruct->title == "Outright" && in_array($market->title,["TOP40","DTOP","DCAP"])) 
                {
                    $userMarketRequest = factory(App\Models\MarketRequest\UserMarketRequest::class)->create([
                        "market_id" =>  $market->id,
                        "trade_structure_id" => $tradeStruct->id,
                    ]);
                
               
                    $user = factory(App\Models\UserManagement\User::class)->create();

                    // user market
                    $userMarket =  $userMarketRequest->userMarkets()->create([
                        'user_id' => $user->id,
                        'is_on_hold' => true
                    ]);

                    // market negotiation
                    $marketNegotiation = $userMarket->marketNegotiations()->create([
                        'user_id'               =>  $user->id,
                        'counter_user_id'       =>  $userMarketRequest->user_id,
                        'market_negotiation_id' =>  null,
                        'bid' => 10,
                        'offer' => 16,
                        'bid_qty' => 500,
                        'offer_qty' => 300,
                        'future_reference'      =>  0,
                        'has_premium_calc'      =>  false,

                        'is_private'            =>  true,
                    ]);


                    $userMarket->current_market_negotiation_id = $marketNegotiation->id;
                    $userMarket->save();
                    var_dump($user->email);


                }
                 
            }

        }
    }
}
