<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\TradingAccount::class, function (Faker $faker) {
    return [
		"user_id" => function(){
            return factory(App\Models\UserManagement\User::class)->create()->id;
        },
		"market_id" => function(){
            return factory(App\Models\StructureItems\Market::class)->create()->id;
        },
		"safex_number" => $faker->creditCardNumber,
		"sub_account" => $faker->creditCardNumber
    ];
});
