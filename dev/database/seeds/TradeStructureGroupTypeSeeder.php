<?php

use Illuminate\Database\Seeder;

class TradeStructureGroupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $groups = config('trade_structure_groups');

       foreach ($groups as $group) 
        {
       		factory(\App\Models\StructureItems\TradeStructureGroupType::class,1)->create([
       			'title' => $group['title']
       		]);
        }
    }
}
