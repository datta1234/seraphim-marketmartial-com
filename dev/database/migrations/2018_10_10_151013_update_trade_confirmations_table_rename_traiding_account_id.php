<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeConfirmationsTableRenameTraidingAccountId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->dropForeign(['traiding_account_id']);
            $table->dropColumn(['traiding_account_id',]);

            $table->integer('trading_account_id')->unsigned();
            $table->foreign('trading_account_id')
                ->references('id')->on('trading_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {
           
            $table->integer('traiding_account_id')->nullable()->unsigned();
            $table->foreign('traiding_account_id')
                ->references('id')->on('trading_accounts');

            $table->dropForeign(['trading_account_id']);
            $table->dropColumn(['trading_account_id',]);
        });
    }
}
