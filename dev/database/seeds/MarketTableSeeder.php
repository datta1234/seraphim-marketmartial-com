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
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => 1,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => true
        ]);

        $DTOP = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'DTOP',
            'description' => null,
            'is_seldom' => true,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => 1,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => true
        ]);

        $DCAP = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'DCAP',
            'description' => null,
            'is_seldom' => true,
            'has_deadline'=> true,
            'needs_spot'=>false,
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


        /*
        *   Option Switch Variants
        */
        $OptionSwitchesTOP40 = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'Options Switches (TOP40)',
            'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => $TOP40->market_type_id,
            'parent_id' =>  $TOP40->id,
            'is_displayed'  =>  false,
            'is_selectable' => false
        ]);
        $OptionSwitchesDTOP = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'Options Switches (DTOP)',
            'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => $DTOP->market_type_id,
            'parent_id' =>  $DTOP->id,
            'is_displayed'  =>  false,
            'is_selectable' => false
        ]);
        $OptionSwitchesDCAP = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'Options Switches (DCAP)',
            'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => $DCAP->market_type_id,
            'parent_id' =>  $DCAP->id,
            'is_displayed'  =>  false,
            'is_selectable' => false
        ]);
        $OptionSwitchesSINGLES = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'Options Switches (DCAP)',
            'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> true,
            'market_type_id' => $SINGLES->market_type_id,
            'parent_id' =>  $SINGLES->id,
            'is_displayed'  =>  false,
            'is_selectable' => false
        ]);


        $DELTAONE = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'DELTA ONE',
            'description' => "",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>true,
            'has_negotiation'=> true,
            'has_rebate'=> false,
            'market_type_id' => 2,
            'parent_id' =>  null,
            'is_displayed'  =>  true,
            'is_selectable' => false
        ]);

        /*
        *   DELTA ONE Markets
        */
        $DELTAONE_EFP = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'EFP',
            'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>true,
            'has_negotiation'=> true,
            'has_rebate'=> false,
            'market_type_id' => $DELTAONE->market_type_id,
            'parent_id' =>  $DELTAONE->id,
            'is_displayed'  =>  false,
            'is_selectable' => false
        ]);
        $DELTAONE_ROLL = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'ROLL',
            'description' => "",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>false,
            'has_negotiation'=> true,
            'has_rebate'=> false,
            'market_type_id' => $DELTAONE->market_type_id,
            'parent_id' =>  $DELTAONE->id,
            'is_displayed'  =>  false,
            'is_selectable' => true
        ]);
        $DELTAONE_EFP_SWITCH = factory(App\Models\StructureItems\Market::class)->create([
            'title'=> 'EFP Switches',
            'description' => "(note: this is different to the Options Switches above, as these are placed under DELTA ONE)",
            'is_seldom' => false,
            'has_deadline'=> true,
            'needs_spot'=>true,
            'has_negotiation'=> true,
            'has_rebate'=> false,
            'market_type_id' => $DELTAONE->market_type_id,
            'parent_id' =>  $DELTAONE->id,
            'is_displayed'  =>  false,
            'is_selectable' => true
        ]);
    }
}
