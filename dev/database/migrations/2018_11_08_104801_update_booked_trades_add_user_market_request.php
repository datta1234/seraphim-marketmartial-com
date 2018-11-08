<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBookedTradesAddUserMarketRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('booked_trades', function (Blueprint $table){
            $table->dropForeign(['market_id']);
            $table->dropForeign(['stock_id']);
            
            $table->dropColumn('market_id');
            $table->dropColumn('stock_id');

            $table->boolean('is_purchase');
            $table->integer('user_market_request_id')->nullable()->unsigned();

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
        Schema::table('booked_trades', function (Blueprint $table){
            $table->dropForeign(['user_market_request_id']);

            $table->dropColumn(['user_market_request_id']);

            $table->integer('market_id')->unsigned()->nullable();
            $table->integer('stock_id')->unsigned()->nullable();


            $table->foreign('market_id')
                ->references('id')->on('markets');

            $table->foreign('stock_id')
                ->references('id')->on('stocks');

        });
        
    }
}
