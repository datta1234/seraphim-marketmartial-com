<?php

use Illuminate\Database\Seeder;

class UserMarketRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $TradeStructures = App\Models\StructureItems\TradeStructure::all()->keyBy('title');

        if(!$TradeStructures['Outright']) {
            $TradeStructures['Outright'] = factory(App\Models\StructureItems\TradeStructure::class,'Outright')->create();
        }
        if(!$TradeStructures['Risky']) {
            $TradeStructures['Risky'] = factory(App\Models\StructureItems\TradeStructure::class,'Risky')->create();
        }
        if(!$TradeStructures['Calendar']) {
            $TradeStructures['Calendar'] = factory(App\Models\StructureItems\TradeStructure::class,'Calendar')->create();
        }
        if(!$TradeStructures['Fly']) {
            $TradeStructures['Fly'] = factory(App\Models\StructureItems\TradeStructure::class,'Fly')->create();
        }

        factory(App\Models\MarketRequest\UserMarketRequest::class, 2)->create([
            "trade_structure_id" => $TradeStructures['Outright']
        ]);

        factory(App\Models\MarketRequest\UserMarketRequest::class, 2)->create([
            "trade_structure_id" => $TradeStructures['Risky']
        ]);

        factory(App\Models\MarketRequest\UserMarketRequest::class, 2)->create([
            "trade_structure_id" => $TradeStructures['Calendar']
        ]);

        factory(App\Models\MarketRequest\UserMarketRequest::class, 2)->create([
            "trade_structure_id" => $TradeStructures['Fly']
        ]);
    }
}
