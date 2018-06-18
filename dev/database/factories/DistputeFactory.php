<?php

use Faker\Generator as Faker;

// App\Models\TradeConfirmations\DistputeStatus

$factory->define( App\Models\TradeConfirmations\Distpute::class, function (Faker $faker) {
    return [
    	
        'title'						=> $faker->word,
        'send_user_id' 				=> factory(App\Models\UserManagement\User::class)->create()->id,
		'receiving_user_id'			=> factory(App\Models\UserManagement\User::class)->create()->id,
		'distpute_status_id'		=> factory(App\Models\TradeConfirmations\DistputeStatus::class)->create()->id,
		'trade_confirmation_id'		=> factory(App\Models\TradeConfirmations\TradeConfirmation::class)->create()->id,
		
    ];
});
