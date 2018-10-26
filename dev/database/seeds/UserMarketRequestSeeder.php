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



               

                //for outright make some that are on hold
                if($tradeStruct->title == "Outright" && in_array($market->title,["TOP40","DTOP","DCAP"])) 
                {
                    //Needed more data for stats
                    for($i = 0; $i <= 2; $i++) {

                        $interestUser = App\Models\UserManagement\User::inRandomOrder()->first(); 
                        $markerUser = App\Models\UserManagement\User::inRandomOrder()->where('organisation_id','<>',$interestUser->organisation_id)->first(); 


                
                        $userMarketRequest = factory(App\Models\MarketRequest\UserMarketRequest::class)->create([
                            "market_id" =>  $market->id,
                            "trade_structure_id" => $tradeStruct->id,
                            "user_id" => $interestUser->id,
                        ]);
                    
                   

                        // user market
                        $userMarket =  $userMarketRequest->userMarkets()->create([
                            'user_id' => $markerUser->id,
                            'is_on_hold' => true
                        ]);

                        // market negotiation
                        $marketNegotiation = $userMarket->marketNegotiations()->create([
                            'user_id'               =>  $markerUser->id,
                            'counter_user_id'       =>  $userMarketRequest->user_id,
                            'market_negotiation_id' =>  null,
                            'bid' => rand(0,10),
                            'offer' => rand(11,20),
                            'bid_qty' => rand(500,1000),
                            'offer_qty' => rand(500,1000),
                            'future_reference'      =>  rand(1000,50000),
                            'has_premium_calc'      =>  false,
                            'is_accepted'           => rand(0,1) == 1,
                            'is_private'            =>  true,
                        ]);


                        $userMarket->current_market_negotiation_id = $marketNegotiation->id;
                        $userMarket->save();
                    }



                }
                 
            }

        }
    }
}
