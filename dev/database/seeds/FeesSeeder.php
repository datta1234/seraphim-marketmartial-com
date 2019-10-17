<?php

use Illuminate\Database\Seeder;

class FeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tradeStructures = config('tradestructures');
        $itemTypes = \App\Models\StructureItems\ItemType::all();

        foreach ($tradeStructures as $tradeStructure) {
        	$tradeStructureModel = \App\Models\StructureItems\TradeStructure::where('title',$tradeStructure['title'])
        							->first();

       		if(is_null($tradeStructureModel)) {
       			throw new Exception("Unable to seed Fees, No Trade Structure found for: ".$tradeStructure['title']);
       		}

            $tradeStructureModel->update(['has_structure_fee' => $tradeStructure['has_structure_fee']]);

        	foreach ($tradeStructure['trade_structure_group'] as $group) {
				        		
    			// Find tradeStructureGroup (__user_market_request_group__)
    			$tradeStructureGroupModel = \App\Models\StructureItems\TradeStructureGroup::where([
    											['title',$group['title']],
    											['trade_structure_group_type_id', 1],
    											['trade_structure_id', $tradeStructureModel->id]
    										])
    										->first();
    										
    			if(is_null($tradeStructureGroupModel)) {
    				throw new Exception(
    					"Unable to seed Fees, No Trade Structure Group found for: "
    					.$tradeStructure['title']
    					." of trade_structure_group_type 1 and Trade Structure "
    					.$tradeStructureModel->title
    				);
    			}


                foreach ($group['items'] as $item) {
        			// Only add update items
                    if(array_key_exists('update_seed',$item) && $item['update_seed']) {
        				$itemType = $itemTypes->firstWhere('title',$item['type']);
        				factory(\App\Models\StructureItems\Item::class)->create([
                            'title' => $item['title'],
                            'trade_structure_group_id' => $tradeStructureGroupModel->id,
                            'item_type_id' => $itemType->id
                        ]);
        			}

        		}
			
                if(
                    isset($group['trade_confirmation_group']['fees']) 
                    && isset($group['trade_confirmation_group']['fees']['items'])
                ) {
    				$feesGroupModel = factory(\App\Models\StructureItems\TradeStructureGroup::class)->create([
		                                'title' => $group['trade_confirmation_group']['fees']['title'],
		                                'trade_structure_id' => $tradeStructureModel->id,
		                                'force_select'=> null,
		                                'trade_structure_group_id'=>$tradeStructureGroupModel->id,
		                                'trade_structure_group_type_id'=> 3
		                            ]);

    				foreach ($group['trade_confirmation_group']['fees']['items'] as $item) {
                        // Only add update items
                        if(array_key_exists('update_seed',$item) && $item['update_seed']) {
	                        $itemType = $itemTypes->firstWhere('title',$item['type']);
	                        factory(\App\Models\StructureItems\Item::class)->create([
	                            'title' => $item['title'],
	                            'trade_structure_group_id' => $feesGroupModel->id,
	                            'item_type_id' => $itemType->id
	                        ]);
                    	}
                    } 
                }
        	}  
        }
    }
}
