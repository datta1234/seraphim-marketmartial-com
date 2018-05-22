<?php

use Illuminate\Database\Seeder;

class MarketConditionCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('market_condition_categories')->insert([
            [
                'id' => 1,
                'title' => 'Fill or kill'
            ],
            [
                'id' => 2,
                'title' => 'Meet in the Middle'
            ],
            [
                'id' => 3,
                'title' => 'Buy/Sell at Best'
            ],
        ]);
    }
}
