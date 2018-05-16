<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketNegotiationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_negotiations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('user_id')->unsigned();
            $table->integer('market_negotiation_id')->unsigned()->nullable();
            $table->integer('user_market_id')->unsigned();
            $table->integer('market_negotiation_status_id')->unsigned();
            
            $table->double('bid', 11, 2)->nullable();
            $table->double('offer', 11, 2)->nullable();
            $table->double('bid_premium', 11, 2)->nullable();
            $table->double('offer_premium', 11, 2)->nullable();
            $table->double('future_reference', 11, 2)->nullable();
            
            $table->double('spot_price', 11, 2)->nullable();
            $table->boolean('has_premium_calc');
            $table->boolean('is_repeat');
            $table->boolean('is_accepted');
            
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users');

            $table->foreign('market_negotiation_id')
                ->references('id')->on('market_negotiations');

            $table->foreign('user_market_id')
                ->references('id')->on('user_markets');

            $table->foreign('market_negotiation_status_id')
                ->references('id')->on('market_negotiation_statuses');
        });

        Schema::table('user_markets', function (Blueprint $table){
            $table->foreign('current_market_negotiation_id')
                ->references('id')->on('market_negotiations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_markets', function (Blueprint $table){
            $table->dropForeign(['current_market_negotiation_id']);
        });
        
        Schema::dropIfExists('market_negotiations');
    }
}
