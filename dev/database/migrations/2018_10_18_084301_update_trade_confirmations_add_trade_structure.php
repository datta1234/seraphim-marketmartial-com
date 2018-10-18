<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTradeConfirmationsAddTradeStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {

            $table->integer('trade_structure_id')->nullable()->unsigned();
            $table->integer('user_market_request_id')->nullable()->unsigned();
            $table->boolean('is_put')->nullable();


            $table->foreign('trade_structure_id')->references('id')->on('trade_structures');
            $table->foreign('user_market_request_id')->references('id')->on('user_market_requests');           
        });

        self::seedMarketRequest();

        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->integer('trade_structure_id')->nullable(false)->unsigned()->change();
            $table->integer('user_market_request_id')->nullable(false)->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->dropForeign(['trade_structure_id']);
            $table->dropForeign(['user_market_request_id']);
      
             $table->dropColumn([
                'trade_structure_id',
                'user_market_request_id',
                'is_put',
            ]);
        });
    }

    private static function seedMarketRequest()
    {

      DB::table('trade_confirmations')
        ->join('trade_negotiations', function ($join) {
            $join->on('trade_confirmations.trade_negotiation_id', '=', 'trade_negotiations.id');
        })->join('market_negotiations', function ($join) {
            $join->on('trade_negotiations.market_negotiation_id', '=', 'market_negotiations.id');
        })->join('user_markets', function ($join) {
            $join->on('market_negotiations.user_market_id', '=', 'user_markets.id');
        })->join('user_market_requests', function ($join) {
            $join->on('user_markets.user_market_request_id', '=', 'user_market_requests.id');
        })
        ->update([
            'trade_confirmations.user_market_request_id'=> DB::raw("user_market_requests.id"),
            'trade_confirmations.trade_structure_id'    => DB::raw("user_market_requests.trade_structure_id"),
        ]);
    }
}
