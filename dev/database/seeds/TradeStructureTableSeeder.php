<?php

use Illuminate\Database\Seeder;

class TradeStructureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tradeStructures = config('tradestructures');

        foreach ($tradeStructures as $tradeStructure) 
        {
            $tradeStructureModel = factory(\App\Models\StructureItems\TradeStructure::class,$tradeStructure['title'],1)->create()
                ->each(function($tradeStructureModel) use ($tradeStructure){

                    foreach ($tradeStructure['trade_structure_group'] as $group) 
                    {   
                   
                        $tradeStructureGroupModel = factory(\App\Models\StructureItems\TradeStructureGroup::class)->create([
                                   'title' => $group['title'],
                                   'trade_structure_id' => $tradeStructureModel->id,
                                   'force_select'=> $group['force_select']
                        ]);

                        foreach ($group['items'] as $item) 
                        {
                            $itemType = \App\Models\StructureItems\ItemType::where('title',$item['type'])->first();
                        

                            factory(\App\Models\StructureItems\Item::class)->create([
                                'title' => $item['title'],
                                'trade_structure_group_id' => $tradeStructureGroupModel->id,
                                'item_type_id' => $itemType->id
                            ]); 
                        }
                    }
                });

        }

        $tradeStructures = \App\Models\StructureItems\TradeStructure::all()->keyBy('title');
        $marketTypes = \App\Models\StructureItems\MarketType::all()->keyBy('title');

        $marketTypes['Index Option']->tradeStructures()->sync([
            $tradeStructures['Outright']->id,
            $tradeStructures['Risky']->id,
            $tradeStructures['Calendar']->id,
            $tradeStructures['Fly']->id,
            $tradeStructures['Option Switch']->id,
        ]);
        $marketTypes['Delta One(EFPs, Rolls and EFP Switches)']->tradeStructures()->sync([
            $tradeStructures['EFP']->id,
            $tradeStructures['Rolls']->id,
            $tradeStructures['EFP Switch']->id,
        ]);
        $marketTypes['Single Stock Options']->tradeStructures()->sync([
            $tradeStructures['Outright']->id,
            $tradeStructures['Risky']->id,
            $tradeStructures['Calendar']->id,
            $tradeStructures['Fly']->id,
            $tradeStructures['Option Switch']->id,
        ]);
    }
}
