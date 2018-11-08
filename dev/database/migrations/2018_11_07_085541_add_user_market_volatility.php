<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserMarketVolatility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_market_volatility', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_market_id')->unsigned();
            $table->integer('user_market_request_group_id')->unsigned();
            
            $table->double('volatility', 11, 2)->nullable();

            $table->timestamps();
            
            $table->foreign('user_market_id')
                ->references('id')->on('user_markets');

            $table->foreign('user_market_request_group_id')
                ->references('id')->on('user_market_request_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_market_volatility');
    }
}
