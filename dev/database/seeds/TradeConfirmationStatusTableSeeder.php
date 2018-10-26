<?php

use Illuminate\Database\Seeder;

class TradeConfirmationStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tradeConfirmations = config('marketmartial.trade_confirmations_statuses');
		foreach ($tradeConfirmations as $tradeConfirmation) 
		{

    		$tradeStructureModel = factory(\App\Models\TradeConfirmations\TradeConfirmationStatus::class,$tradeConfirmation['title'],1)->create();
		}
	}
}
