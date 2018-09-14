<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropMarketNegotiationConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_negotiation_condition', function (Blueprint $table) {
            $table->dropForeign(['market_negotiation_id']);
            $table->dropForeign(['market_condition_id']);
        });
        Schema::dropIfExists('market_negotiation_condition');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('market_negotiation_condition', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('market_negotiation_id')->unsigned();
            $table->integer('market_condition_id')->unsigned();
            $table->timestamps();

            $table->foreign('market_negotiation_id')
                ->references('id')->on('market_negotiations');

            $table->foreign('market_condition_id')
                ->references('id')->on('market_conditions');
        });
    }
}
