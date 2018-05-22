<?php

use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('item_types')->insert([
            [
                'id' => 1,
                'title' => 'expiration date',
                'validation_rule' => 'required|date|after:today'
            ],
            [
                'id' => 2,
                'title' => 'double',
                'validation_rule' => 'required|numeric'
            ]
        ]);
    }
}
