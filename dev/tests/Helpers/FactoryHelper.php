<?php

namespace Tests\Factory;

class FactoryHelper
{
	public static function setUpTradeStructures()
	{

		// factory(\App\Models\UserManagement\Organisation::class,8)->create();
		// exit;

		$tradeStructures = config('tradestructures');
		// need the types

		factory(\App\Models\StructureItems\ItemType::class)->create([
			'title' => 'Expiration Date',
			'validation_rule' => 'required|date|after:today',
		]);	

		factory(\App\Models\StructureItems\ItemType::class)->create([
			'title' => 'Double',
			'validation_rule' => 'required|numeric',
		]);	


		foreach ($tradeStructures as $tradeStructure) 
		{
			$tradeStructureModel = factory(\App\Models\StructureItems\TradeStructure::class,$tradeStructure['title'],1)->create()
				
				->each(function($tradeStructureModel) use ($tradeStructure){

					foreach ($tradeStructure['trade_structure_group'] as $group) 
					{	
						$tradeStructureGroupModel = factory(\App\Models\StructureItems\TradeStructureGroup::class)->create([
								   'title' => $group['title'],
								   'trade_structure_id' => $tradeStructureModel->id
						]);

						foreach ($group['items'] as $item) 
						{
							$itemType = \App\Models\StructureItems\ItemType::where('title',$item['type'])->first();
						

							factory(\App\Models\StructureItems\Item::class)->create([
								'title' => $item['title'],
								'trade_structure_group_id' => $tradeStructureGroupModel->id,
								'item_type_id' => $itemType->id
							]);	
						}		
					}
				});

			}
	}    
}
