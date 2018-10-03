<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMarketsUserManagementForeignKeyAdditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trading_accounts', function (Blueprint $table){
            $table->foreign('market_id')
                ->references('id')->on('markets');
        });

        Schema::table('user_market_watched', function (Blueprint $table){
            $table->foreign('market_id')
                ->references('id')->on('markets');
        });

        Schema::table('user_market_interests', function (Blueprint $table){
            $table->foreign('market_id')
                ->references('id')->on('markets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trading_accounts', function (Blueprint $table){
            $table->dropForeign(['market_id']);
        });

        Schema::table('user_market_watched', function (Blueprint $table){
            $table->dropForeign(['market_id']);
        }); 

        Schema::table('user_market_interests', function (Blueprint $table){
            $table->dropForeign(['market_id']);
        });  
    }
}
