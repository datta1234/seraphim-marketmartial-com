<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	 /**
         * Required seed to be ran so that the application can operate
         *
         */
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        
        $this->call(MarketTypeTableSeeder::class);
        $this->call(MarketTableSeeder::class);
        $this->call(StockTableSeeder::class);
        $this->call(SafexExpirationDateTableSeeder::class);
        $this->call(UserNotificationTypeTableSeeder::class);
        $this->call(DefaultLabelTableSeeder::class);
        $this->call(InterestTableSeeder::class);

        //enable selection of trade structures
        
        $this->call(ItemTypeSeeder::class);
        
        $this->call(TradeStructureGroupTypeSeeder::class);
        $this->call(TradeStructureTableSeeder::class);
        
        //possible statuses for the trade_confirmations
        $this->call(TradeConfirmationStatusTableSeeder::class);

        
        
    }
}
