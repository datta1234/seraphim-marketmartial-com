<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trade_negotiation_id')->unsigned();
            $table->integer('market_negotiation_id')->unsigned();
            $table->integer('user_market_id')->unsigned();
            $table->integer('initiate_user_id')->unsigned();
            $table->integer('recieving_user_id')->unsigned();
            $table->integer('trade_status_id')->unsigned();
            $table->double('quantity', 11, 2);
            $table->timestamps();

            $table->foreign('trade_negotiation_id')
                ->references('id')->on('trade_negotiations');

            $table->foreign('market_negotiation_id')
                ->references('id')->on('market_negotiations');

            $table->foreign('user_market_id')
                ->references('id')->on('user_markets');

            $table->foreign('initiate_user_id')
                ->references('id')->on('users');

            $table->foreign('recieving_user_id')
                ->references('id')->on('users');

            $table->foreign('trade_status_id')
                ->references('id')->on('trade_status');
        });

        Schema::table('rebates', function (Blueprint $table){
            $table->foreign('trade_id')
                ->references('id')->on('trades');
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
            $table->dropForeign(['trade_id']);
        });
        
        Schema::dropIfExists('trades');
    }
}
