<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRebatesAddTradeConfirmation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('rebates', function (Blueprint $table){
            $table->integer('trade_confirmation_id')->unsigned()->nullable();
            $table->double('amount',11, 2);

            $table->foreign('trade_confirmation_id')
            ->references('id')->on('trade_confirmations');

         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rebates', function (Blueprint $table){
            $table->dropForeign(['trade_confirmation_id']);
            $table->dropColumn(['trade_confirmation_id','amount']);
        });
    }
}
