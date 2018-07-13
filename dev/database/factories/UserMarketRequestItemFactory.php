<?php

use Faker\Generator as Faker;

$factory->define(App\Models\MarketRequest\UserMarketRequestItem::class, function (Faker $faker) {
    return [
        "user_market_request_group_id" => function() {
            return factory(App\Models\UserMarket\UserMarketRequestGroup::class)->create()->id;
        },
        "item_id" => function() {
            return factory(App\Models\StructureItems\Item::class)->create()->id;
        },
        "title" => function($umrItem) {
            return App\Models\StructureItems\Item::find($umrItem['item_id'])->title;
        },
        "type" => function($umrItem) {
            return App\Models\StructureItems\Item::find($umrItem['item_id'])->itemType->title;
        },
        "value" => function($umrItem) use ($faker) {
            return App\Models\StructureItems\Item::find($umrItem['item_id'])->itemType->title == "Expiration Date" ? $faker->date('My') : $faker->randomNumber(8) ;
        }
    ];
});

