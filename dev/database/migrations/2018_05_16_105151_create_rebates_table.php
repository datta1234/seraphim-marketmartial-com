<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRebatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rebates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('user_market_id')->unsigned();
            $table->integer('organisation_id')->unsigned();
            $table->integer('user_market_request_id')->unsigned();
            $table->integer('trade_id')->unsigned();
            $table->integer('booked_trade_id')->unsigned()->nullable();
            $table->boolean('is_paid');
            $table->date('trade_date');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users');

            $table->foreign('user_market_id')
                ->references('id')->on('user_markets');

            $table->foreign('organisation_id')
                ->references('id')->on('organisations');

            $table->foreign('user_market_request_id')
                ->references('id')->on('user_market_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rebates');
    }
}
