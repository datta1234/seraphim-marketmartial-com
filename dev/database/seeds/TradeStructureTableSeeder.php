<?php

use Illuminate\Database\Seeder;

class TradeStructureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('trade_structures')->insert([
            [
                'id' => 1,
                'title' => 'Outright'
            ],
            [
                'id' => 2,
                'title' => 'Risky'
            ],
            [
                'id' => 3,
                'title' => 'Calendar'
            ],
            [
                'id' => 4,
                'title' => 'Fly'
            ],
            [
                'id' => 5,
                'title' => 'EFP'
            ],
            [
                'id' => 6,
                'title' => 'Rolls'
            ]
        ]);
    }
}
