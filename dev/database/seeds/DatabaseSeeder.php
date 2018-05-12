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

    	// data required for the system to be able to run
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(TradeStructureTableSeeder::class);
        $this->call(MarketConditionCategoryTableSeeder::class);
        $this->call(MarketConditionTableSeeder::class);
        $this->call(MarketTypeTableSeeder::class);
        $this->call(MarketTableSeeder::class);
        $this->call(StockTableSeeder::class);
        $this->call(SafexExpirationDateTableSeeder::class);

        
        //start dummy data import
        $this->call(OrganisationTableSeeder::class);

    }
}
