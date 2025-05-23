<?php

use Illuminate\Database\Seeder;

class MarketConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('market_conditions')->insert([
            [
                'id' => 1,
                'title' => 'Repeat all the way',
                'alias' => 'Repeat ATW',
                'market_condition_category_id' => null
            ],
            [
                'id' => 2,
                'title' => 'Prefer to kill (20min Fok)',
                'alias' => 'FoK: kill',
                'market_condition_category_id' => 1
            ],
            [
                'id' => 3,
                'title' => 'Happy to Spin',
                'alias' => 'FoK: fill',
                'market_condition_category_id' => 1
            ],
            [
                'id' => 4,
                'title' => 'Propose (Private)',
                'alias' => 'Propose (Private)',
                'market_condition_category_id' => null
            ],
            [
                'id' => 5,
                'title' => 'Buy in the middle',
                'alias' => 'Buy at mid(Private)',
                'market_condition_category_id' => 2
            ],
            [
                'id' => 6,
                'title' => 'Sell in the middle',
                'alias' => 'Sell at best',
                'market_condition_category_id' => 2
            ],
          	[
                'id' => 7,
                'title' => 'Buy at best',
                'alias' => 'Sell at best',
            	'market_condition_category_id' => 3
            ],
            [
                'id' => 8,
                'title' => 'Sell at best',
                'alias' => 'Sell at best',
            	'market_condition_category_id' => 3
            ],
            [
                'id' => 9,
                'title' => 'OCO',
                'alias' => 'OCO',
            	'market_condition_category_id' => null
            ],
            [
                'id' => 10,
                'title' => 'Subject',
                'alias' => 'Subject',
            	'market_condition_category_id' => null
            ],
        ]);
    }
}
