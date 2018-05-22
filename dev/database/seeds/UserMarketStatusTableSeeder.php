<?php

use Illuminate\Database\Seeder;

class UserMarketStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('user_market_statuses')->insert([
            [
				'title'=> 'Pending: initiate traders response',
            ],
            [
				'title'=> 'On Hold',
            ],
            [
				'title'=> 'Pending: under trade negotiation',
            ],
            [
				'title'=> 'Complete',
            ],
        ]);
    }
}
