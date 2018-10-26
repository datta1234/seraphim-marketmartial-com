<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBookedTradesRebatesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rebates', function (Blueprint $table){
            $table->dropForeign(['booked_trade_id']);
            $table->dropColumn(['booked_trade_id']);
        });

        Schema::table('booked_trades', function (Blueprint $table){
            $table->integer('rebate_trade_id')->unsigned()->nullable();

            $table->foreign('rebate_trade_id')
                ->references('id')->on('booked_trades');
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
            $table->integer('booked_trade_id')->unsigned()->nullable();

            $table->foreign('booked_trade_id')
                ->references('id')->on('booked_trades');
        });

        Schema::table('booked_trades', function (Blueprint $table){
            $table->dropForeign(['rebate_trade_id']);
            $table->dropColumn(['rebate_trade_id']);
        });
    }
}
