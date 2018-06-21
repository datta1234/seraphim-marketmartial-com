<?php

namespace Tests\Helper;

class FactoryHelper
{
	public static function setUpTradeStructures()
	{

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

	public static function setUpTradeConditions()
	{
		$conditionCategories = config('marketmartial.market_condition_category');
		$conditions = config('marketmartial.market_conditions');

		foreach ($conditionCategories as $conditionCategory) {
			factory(\App\Models\Market\MarketConditionCategory::class)->create([
				'title' =>  $conditionCategory['title']
			]);	
		}

		foreach ($conditions as $condition) {

			factory(\App\Models\Market\MarketCondition::class)->create([
				'title' =>  $condition['title'],
				'alias' =>  $condition['alias'],
				'timeout' => $condition['timeout'],
				'market_condition_category_id' => $condition['market_condition_category'] == null ? null : \App\Models\Market\MarketConditionCategory::where('title',$condition['market_condition_category'])->first()->id
			]);	
		}
	} 


	public static function setUpMarkets()
	{
		$marketTypes = config('marketmartial.market_type');
		$markets = config('marketmartial.markets');

		foreach ($marketTypes as $marketType) {
			factory(\App\Models\StructureItems\MarketType::class)->create([
					'title' =>  $marketType['title']
				]);	
		}

		foreach($markets as $market)
		{
			factory(\App\Models\StructureItems\Market::class)->create([
				'title' => $market['title'] ,
				'description' => $market['description'],
				'is_seldom' => $market['is_seldom'],
				'has_deadline' => $market['has_deadline'],
				'needs_spot' => $market['needs_spot'],
				'has_negotiation' => $market['needs_spot'],
				'has_rebate' => $market['has_rebate'],
				'market_type_id' => \App\Models\StructureItems\MarketType::firstOrCreate(['title' => $market['market_type']]),
				'is_selectable' => $market['is_selectable'],
			]);	
		}

	}
}
