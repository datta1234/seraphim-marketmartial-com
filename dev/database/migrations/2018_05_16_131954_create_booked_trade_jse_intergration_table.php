<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookedTradeJseIntergrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_trade_jse_intergration', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jse_intergration_id')->unsigned();
            $table->integer('booked_trade_id')->unsigned();
            $table->timestamps();

            $table->foreign('jse_intergration_id')
                ->references('id')->on('jse_trade_intergrations');

            $table->foreign('booked_trade_id')
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
        Schema::dropIfExists('booked_trade_jse_intergration');
    }
}
