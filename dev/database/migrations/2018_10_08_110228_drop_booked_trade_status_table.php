<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBookedTradeStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booked_trades', function (Blueprint $table){
            $table->dropForeign(['booked_trade_status_id']);
            $table->dropColumn(['booked_trade_status_id']);
        });

        Schema::dropIfExists('booked_trade_status');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('booked_trade_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });

        Schema::table('booked_trades', function (Blueprint $table){
            $table->integer('booked_trade_status_id')->nullable()->unsigned();

            $table->foreign('booked_trade_status_id')
                ->references('id')->on('booked_trade_status');
        });
    }
}
