<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSafexTradeConfirmationsChangeNominalSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `safex_trade_confirmations` 
            CHANGE `nominal` 
            `nominal` DOUBLE(20,2) 
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
        DB::select('ALTER TABLE `safex_trade_confirmations` 
            CHANGE `nominal` 
            `nominal` DOUBLE(11,2) 
            NOT NULL'
        );
    }
}
