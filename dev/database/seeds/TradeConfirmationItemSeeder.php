<?php

use Illuminate\Database\Seeder;

class TradeConfirmationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$tradeConfirmations = \App\Models\TradeConfirmations\TradeConfirmation::all();

        foreach ($tradeConfirmations as $tradeConfirmation) {
        	
        	$marketNegotiation = $tradeConfirmation->tradeNegotiation->marketNegotiation;
	        $marketRequest = $marketNegotiation->userMarket->userMarketRequest;
	        $groups =  $marketRequest->tradeStructure->tradeStructureGroups()->where('trade_structure_group_type_id',3)->get();
	        $is_offer = $tradeConfirmation->tradeNegotiation->is_offer;

	        foreach($groups as $tradeStructureGroup) {
	            
	            $tradeGroup = $tradeConfirmation->tradeConfirmationGroups()->create([
	                'trade_structure_group_id'  =>  $tradeStructureGroup->id,
	                'trade_confirmation_id'     =>  $tradeConfirmation->id,
	                "is_options"                 =>  $tradeStructureGroup->title == "Options Group" ? 1: 0,
	                'user_market_request_group_id' => $marketRequest->userMarketRequestGroups()->where('trade_structure_group_id',$tradeStructureGroup->trade_structure_group_id)->first()->id,
	            ]);


	            foreach($tradeStructureGroup->items as $item) {

	                $value = null;

	                switch ($item->title) {
	                    case 'is_offer':
	                        $value = $is_offer ? 1 : 0;
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
	                     case 'future':
	                         $value = rand(100,1000);
	                        break;
	                     case 'contract':
	                         $value = rand(100,1000);
	                        break;
	                }


	                factory(App\Models\TradeConfirmations\TradeConfirmationItem::class)->create([
	                    'item_id' => $item->id,
	                    'trade_confirmation_group_id' => $tradeGroup->id,
	                    'title' => $item->title,
	                    'value' =>  $value
	                ]);
	            }
	        }
        }
    }
}
