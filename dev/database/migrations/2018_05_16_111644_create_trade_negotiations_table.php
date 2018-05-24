<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeNegotiationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_negotiations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_market_id')->unsigned();
            $table->integer('trade_negotiation_id')->unsigned()->nullable();
            $table->integer('market_negotiation_id')->unsigned();
            $table->integer('initiate_user_id')->unsigned();
            $table->integer('recieving_user_id')->unsigned();
            $table->integer('trade_negotiation_status_id')->unsigned();
            $table->boolean('traded');

            $table->double('quantity', 11, 2);
            
            $table->boolean('is_offer');
            $table->boolean('is_distpute');
            
            $table->timestamps();

            $table->foreign('user_market_id')
                ->references('id')->on('user_markets');

            $table->foreign('trade_negotiation_id')
                ->references('id')->on('trade_negotiations');

            $table->foreign('market_negotiation_id')
                ->references('id')->on('market_negotiations');

            $table->foreign('initiate_user_id')
                ->references('id')->on('users');

            $table->foreign('recieving_user_id')
                ->references('id')->on('users');

            $table->foreign('trade_negotiation_status_id')
                ->references('id')->on('trade_negotiation_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_negotiations');
    }
}
