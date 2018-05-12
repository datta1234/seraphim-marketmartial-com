<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMarketRequestTradablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_market_request_tradables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_market_request_id')->unsigned();
            $table->integer('market_id')->unsigned()->nullable();
            $table->integer('stock_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_market_request_id')
                ->references('id')->on('user_market_requests');

            $table->foreign('market_id')
                ->references('id')->on('markets');

            $table->foreign('stock_id')
                ->references('id')->on('stocks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_market_request_tradables');
    }
}
