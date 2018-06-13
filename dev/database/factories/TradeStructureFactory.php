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



/*

$factory->defineAs(App\User::class, 'admin', function ($faker) use ($factory) {
    $post = $factory->raw('App\User');

    return array_merge($post, ['admin' => true]);
});
*/