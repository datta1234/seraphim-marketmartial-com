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
				'title'=> 'Options'
            ],
            [
            	'id'=> 2,
				'title'=> 'DELTA ONE'
            ]
        ]);
    }
}
