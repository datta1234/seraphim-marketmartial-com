<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_markets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('user_market_request_id')->unsigned();
            $table->integer('current_market_negotiation_id')->unsigned()->nullable();
            $table->boolean('is_trade_away')->default(false);
            $table->boolean('is_market_maker_notified')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('users');

            $table->foreign('user_market_request_id')
                ->references('id')->on('user_market_requests');
        });

        Schema::table('user_market_requests', function (Blueprint $table){
            $table->foreign('chosen_user_market_id')
                ->references('id')->on('user_markets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_market_requests', function (Blueprint $table){
            $table->dropForeign(['chosen_user_market_id']);
        });
        
        Schema::dropIfExists('user_markets');
    }
}
