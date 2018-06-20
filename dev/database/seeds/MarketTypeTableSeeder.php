<?php

use Illuminate\Database\Seeder;

class MarketTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           DB::table('market_types')->insert([
            [
            	'id'=> 1,
				'title'=> 'Index Option'
            ],
            [
            	'id'=> 2,
				'title'=> 'Delta One(EFPs, Rolls and EFP Switches)'
            ],
             [
                'id'=> 3,
                'title'=> 'Single Stock Options'
            ]
        ]);
    }
}
