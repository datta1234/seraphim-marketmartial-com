<?php

use Faker\Generator as Faker;
// 
$tradeStructures = config('tradestructures');

foreach ($tradeStructures as $tradeStructure) 
{
	$factory->defineAs(App\Models\StructureItems\TradeStructure::class,$tradeStructure['title'],function (Faker $faker) use ($tradeStructure){
		
		return [
		   'title' => $tradeStructure['title']
		];
	});
}


