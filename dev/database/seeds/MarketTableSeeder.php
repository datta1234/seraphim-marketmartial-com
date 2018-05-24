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
           DB::table('markets')->insert([
            [
				'title'=> 'TOP40',
				'description' => null,
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> true,
				'market_type_id' => 1,
				'is_selectable' => true
            ],
          	[
				'title'=> 'DTOP',
				'description' => null,
				'is_seldom' => true,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> true,
				'market_type_id' => 1,
				'is_selectable' => true
            ],
         	[
				'title'=> 'DCAP',
				'description' => null,
				'is_seldom' => true,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> true,
				'market_type_id' => 1,
				'is_selectable' => true
            ],
            [
				'title'=> 'SINGLES',
				'description' => null,
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> true,
				'market_type_id' => 3,
				'is_selectable' => true
            ],
            [
				'title'=> 'Options Switches',
				'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> true,
				'market_type_id' => 1,
				'is_selectable' => false
            ],
            [
				'title'=> 'EFP',
				'description' => "placed under respective Index market, unless single vs. single, in which case it will be listed under SINGLES",
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>true,
				'has_negotiation'=> true,
				'has_rebate'=> false,
				'market_type_id' => 2,
				'is_selectable' => false
            ],
            [
				'title'=> 'Roll',
				'description' => null,
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>false,
				'has_negotiation'=> true,
				'has_rebate'=> false,
				'market_type_id' => 2,
				'is_selectable' => true
            ],
             [
				'title'=> 'EFP Switches',
				'description' => "(note: this is different to the Options Switches above, as these are placed under DELTA ONE)",
				'is_seldom' => false,
				'has_deadline'=> true,
				'needs_spot'=>true,
				'has_negotiation'=> true,
				'has_rebate'=> false,
				'market_type_id' => 2,
				'is_selectable' => true
            ],


        ]);
    }
}
