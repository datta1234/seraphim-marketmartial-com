<?php

use Illuminate\Database\Seeder;

class UserMarketRequestStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      	DB::table('user_market_request_statuses')->insert([
              [
              'id'=> 1,
              'title'=> 'Pending: traders response',
              ],
              [
              'id'=> 2,
              'title'=> 'Pending: under market negotiation',
              ],
              [
              'id'=> 3,
              'title'=> 'Pending: under trade negotiation',
              ],
              [
              'id'=> 4,
              'title'=> 'Complete',
              ],
        ]);
    }
    }
}
