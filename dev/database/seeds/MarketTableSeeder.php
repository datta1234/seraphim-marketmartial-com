 <?php

use Illuminate\Database\Seeder;

class MarketTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $TOP40 = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'TOP40',
            'description' => null,
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>true,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => 1,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => true
        ]);

        $CTOP = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'CTOP',
            'description' => null,
            'is_seldom' => true,
            'has_deadline'=> true,
            'needs_spot'=>true,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => 1,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => true
        ]);

        $CTOR = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'CTOR',
            'description' => null,
            'is_seldom' => true,
            'has_deadline'=> true,
            'needs_spot'=>true,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => 1,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => true
        ]);

        $SINGLES = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'SINGLES',
            'description' => null,
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => 3,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => true
        ]);

        $DELTAONE = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'DELTA ONE',
            'description' => "",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> false,
            'market_type_id' => 2,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => false
        ]);

    }
}
