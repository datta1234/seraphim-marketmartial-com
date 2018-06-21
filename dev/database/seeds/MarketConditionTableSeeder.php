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
         $conditions = config('marketmartial.market_conditions');
        foreach ($conditions as $condition) {

            factory(\App\Models\Market\MarketCondition::class)->create([
                'title' =>  $condition['title'],
                'alias' =>  $condition['alias'],
                'timeout' => $condition['timeout'],
                'market_condition_category_id' => $condition['market_condition_category'] == null ? null : \App\Models\Market\MarketConditionCategory::where('title',$condition['market_condition_category'])->first()->id
            ]); 
        }
    }
}
