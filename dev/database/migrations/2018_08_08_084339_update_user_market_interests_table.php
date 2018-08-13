<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserMarketInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_market_interests', function (Blueprint $table) {
                $table->dropForeign(['market_id']);
                $table->dropColumn(['market_id']);

                $table->integer('market_type_id')->unsigned();
                $table->foreign('market_type_id')
                    ->references('id')->on('market_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_market_interests', function (Blueprint $table) {
                $table->dropForeign(['market_type_id']);
                $table->dropColumn('market_type_id');

                $table->integer('market_id')->unsigned();
                $table->foreign('market_id')
                    ->references('id')->on('markets');
        });
    }
}
