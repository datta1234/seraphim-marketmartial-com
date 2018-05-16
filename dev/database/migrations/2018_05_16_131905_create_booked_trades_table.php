<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookedTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_trades', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->integer('booked_trade_status_id')->unsigned();
            $table->integer('traiding_account_id')->unsigned();
            $table->integer('trade_confirmation_id')->unsigned()->nullable();
            $table->integer('derivative_id')->unsigned()->nullable();
            $table->integer('stock_id')->unsigned()->nullable();
            
            $table->double('amount', 11, 2);
            $table->boolean('is_confirmed');
            $table->boolean('is_sale');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users');

            $table->foreign('booked_trade_status_id')
                ->references('id')->on('booked_trade_status');

            $table->foreign('traiding_account_id')
                ->references('id')->on('trading_accounts');

            $table->foreign('trade_confirmation_id')
                ->references('id')->on('trade_confirmations');

            $table->foreign('derivative_id')
                ->references('id')->on('derivatives');

            $table->foreign('stock_id')
                ->references('id')->on('stocks');
        });

        Schema::table('rebates', function (Blueprint $table){
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
        Schema::table('rebates', function (Blueprint $table){
            $table->dropForeign(['booked_trade_id']);
        });
        
        Schema::dropIfExists('booked_trades');
    }
}
