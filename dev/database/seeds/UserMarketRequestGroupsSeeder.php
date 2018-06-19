<?php

use Illuminate\Database\Seeder;

class UserMarketRequestGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $marketRequests = App\Models\MarketRequest\UserMarketRequest::all();
        foreach($marketRequests as $marketRequest) {
            
        }
    }
}
