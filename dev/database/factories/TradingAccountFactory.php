<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\TradingAccount::class, function (Faker $faker) {
    return [
		"user_id" => factory(App\Models\UserManagement\User::class)->create()->id,
		"market_id" => factory(App\Models\StructureItems\Market::class)->create()->id,
		"safex_number" => $faker->creditCardNumber,
		"sub_account" => $faker->creditCardNumber
    ];
});
