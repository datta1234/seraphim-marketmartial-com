<?php

use Illuminate\Database\Seeder;

class UserMarketRequestItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $marketRequests = App\Models\MarketRequest\UserMarketRequest::all();

        foreach($marketRequests as $marketRequest) {
            $groups = $marketRequest->tradeStructure->tradeStructureGroups()->where('trade_structure_group_type_id',1)->get();
            foreach($groups as $tradeStructureGroup) {
                
                $tradeGroup = $marketRequest->userMarketRequestGroups()->create([
                    'trade_structure_group_id'  =>  $tradeStructureGroup->id,
                    'is_selected'   =>  rand(0,1) == 1, // TODO: handle depending on trade struct
                ]);

                foreach($tradeStructureGroup->items as $item) {
                    factory(App\Models\MarketRequest\UserMarketRequestItem::class)->create([
                        'item_id' => $item->id,
                        'user_market_request_group_id'  =>  $tradeGroup->id
                    ]);
                }
            }
        }
    }
}
