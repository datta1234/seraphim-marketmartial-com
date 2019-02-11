<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIncreaseDoubleValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `booked_trades` 
            CHANGE `amount` 
            `amount` DOUBLE(14,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `open_interests` 
            CHANGE `open_interest` 
            `open_interest` DOUBLE(14,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `safex_trade_confirmations` 
            CHANGE `nominal` 
            `nominal` DOUBLE(14,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `user_market_volatility` 
            CHANGE `volatility` 
            `volatility` DOUBLE(14,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `rebates` 
            CHANGE `amount` 
            `amount` DOUBLE(14,2) 
            NOT NULL'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select('ALTER TABLE `booked_trades` 
            CHANGE `amount` 
            `amount` DOUBLE(11,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `open_interests` 
            CHANGE `open_interest` 
            `open_interest` DOUBLE(11,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `safex_trade_confirmations` 
            CHANGE `nominal` 
            `nominal` DOUBLE(11,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `user_market_volatility` 
            CHANGE `volatility` 
            `volatility` DOUBLE(11,2) 
            NOT NULL'
        );

        DB::select('ALTER TABLE `rebates` 
            CHANGE `amount` 
            `amount` DOUBLE(11,2) 
            NOT NULL'
        );
    }
}
