<?php

use Faker\Generator as Faker;
$tradeConfirmations = config('marketmartial.trade_confirmations_statuses');

$factory->define(App\Models\TradeConfirmations\TradeConfirmationStatus::class,function (Faker $faker) {
    return [
        'title' => $faker->word
    ];
});

foreach ($tradeConfirmations as $tradeConfirmation) 
{
	$factory->defineAs(App\Models\TradeConfirmations\TradeConfirmationStatus::class,$tradeConfirmation['title'],function (Faker $faker) use($tradeConfirmation) {
	    return [
	    	'id' => $tradeConfirmation['id'],
	        'title' => $tradeConfirmation['title']
	    ];
	});
}