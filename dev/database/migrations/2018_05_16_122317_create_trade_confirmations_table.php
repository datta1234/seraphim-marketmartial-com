<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_confirmations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('send_user_id')->unsigned();
            $table->integer('receiving_user_id')->unsigned();
            $table->integer('trade_id')->unsigned();
            $table->integer('trade_confirmation_statuse_id')->unsigned();
            $table->integer('trade_confirmation_id')->unsigned()->nullable();
            $table->integer('derivative_id')->unsigned()->nullable();
            $table->integer('stock_id')->unsigned()->nullable();
            $table->integer('traiding_account_id')->unsigned();


            $table->double('contracts', 11, 2);
            $table->double('gross_premiums', 11, 2);
            $table->double('net_premiums', 11, 2);

            $table->double('spot_price', 11, 2)->nullable();
            $table->double('future_reference', 11, 2)->nullable();
            $table->double('near_expiery_reference', 11, 2)->nullable();
            $table->double('puts', 11, 2)->nullable();
            $table->double('calls', 11, 2)->nullable();
            $table->double('delta', 11, 2)->nullable();

            $table->boolean('is_confirmed');
            $table->timestamps();

            $table->foreign('send_user_id')
                ->references('id')->on('users');

            $table->foreign('receiving_user_id')
                ->references('id')->on('users');

            $table->foreign('trade_id')
                ->references('id')->on('trades');

            $table->foreign('trade_confirmation_statuse_id')
                ->references('id')->on('trade_confirmation_statuses');

            $table->foreign('trade_confirmation_id')
                ->references('id')->on('trade_confirmations');

            $table->foreign('derivative_id')
                ->references('id')->on('derivatives');

            $table->foreign('stock_id')
                ->references('id')->on('stocks');

            $table->foreign('traiding_account_id')
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
        Schema::dropIfExists('trade_confirmations');
    }
}
