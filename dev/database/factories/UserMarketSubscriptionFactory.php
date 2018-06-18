<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Market\UserMarketSubscription::class, function (Faker $faker) {
    return [
        'user_id'		=> factory(App\Models\UserManagement\User::class)->create()->id,
        'user_market_id'=> factory(App\Models\Market\UserMarket::class)->create()->id,
    ];
});
