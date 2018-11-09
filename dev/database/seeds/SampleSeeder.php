<?php

use Illuminate\Database\Seeder;

class SampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * Start dummy data remove once going live
         *
         */
        if(env('APP_ENV') !== 'production') {
            
            $this->call(UserMarketRequestSeeder::class);
            $this->call(UserMarketRequestTradeablesSeeder::class);
            $this->call(UserMarketRequestItemSeeder::class);

            //remove confirmations for now and using the simple negotiation one
            //$this->call(TradeNegotiationSeeder::class);

            $this->call(TradeConfirmationSeeder::class);
            $this->call(TradeConfirmationItemSeeder::class);
            $this->call(BookedTradeSeeder::class);

            echo "Sample Data Seeded!";
        }
    }
}
