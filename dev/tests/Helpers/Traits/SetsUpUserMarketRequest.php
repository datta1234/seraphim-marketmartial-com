<?php

namespace App\Tests\Helpers;

trait SetsUpUserMarketReqeust {
   
    public function newMarket($market = null, $structure = null) {
        // interest
        $userMarket = [
            'organisation',             =>  null,
            'user',                     =>  null,
            'user_maker',               =>  null,
            'organisation_maker',       =>  null,
            'market',                   =>  null,
            'trade_structure',          =>  null,
            'user_market_request',      =>  null,
            'user_market',              =>  null,
            'user_market_negotiation',  =>  null,
        ];
        $userMarket['organisation'] = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $userMarket['user'] = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$userMarket['organisation']->id
        ]);
        // market maker
        $userMarket['organisation_maker'] = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $userMarket['user_maker'] = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$userMarket['organisation_maker']->id
        ]);
        
        $userMarket['market'] = \App\Models\StructureItems\Market::where('title', $market !== null ? $market : 'TOP40')->first();
        $userMarket['trade_structure'] = \App\Models\StructureItems\TradeStructure::where('title', $structure !== null ? $structure : 'Outright')->first();

        // market request
        $userMarket['user_market_request'] = factory(\App\Models\MarketRequest\UserMarketRequest::class)->create([
            'user_id'               =>  $userMarket['user']->id,
            'trade_structure_id'    =>  $userMarket['trade_structure']->id,
            'market_id'             =>  $userMarket['market']->id,
        ]);
        $userMarket['trade_structure']->tradeStructureGroups->each(function($group) use($userMarket) {
            $userMarketGroup = factory(\App\Models\MarketRequest\UserMarketRequestGroup::class)->create([
                'trade_structure_group_id'  =>  $userMarket['trade_structure']->id,
                'user_market_request_id'    =>  $userMarket['user_market_request']->id
            ]);
            $items = [];
            $group->items->each(function($item) use (&$items, $userMarketGroup) {
                $items[] = factory(\App\Models\MarketRequest\UserMarketRequestItem::class)->create([
                    'user_market_request_group_id'  =>  $userMarketGroup->id,
                    'item_id'    =>  $item->id
                ]);
            });
        });
        // user market
        $userMarket['user_market'] = $userMarket['user_market_request']->userMarkets()->create([
            'user_id' => $userMarket['user']->id
        ]);
        // market negotiation
        $userMarket['user_market_negotiation'] = $userMarket['user_market']->marketNegotiations()->create([
            'user_id'               =>  $userMarket['user']->id,
            'counter_user_id'       =>  $userMarket['user']->id,
            'market_negotiation_id' =>  null,

            'future_reference'      =>  0,
            'has_premium_calc'      =>  false,

            'is_private'            =>  true,
        ]);
        $userMarket['user_market']->currentMarketNegotiation()->associate($userMarket['user_market_negotiation'])->save();
        
        $userMarket['user_market_request_formatted'] = $userMarket['user_market_request']->preFormatted();

    }
}