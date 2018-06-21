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
        $conditionCategories = config('marketmartial.market_condition_category');
        

        foreach ($conditionCategories as $conditionCategory) {
            factory(\App\Models\Market\MarketConditionCategory::class)->create([
                'title' =>  $conditionCategory['title'],
                'market_condition_category_id' => $conditionCategory['market_condition_category'] == null ? null : \App\Models\Market\MarketConditionCategory::where('title',$conditionCategory['market_condition_category'])->first()->id
            ]); 
        }

        
    }
}
