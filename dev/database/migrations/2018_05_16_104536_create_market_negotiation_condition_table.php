<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketNegotiationConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_negotiation_condition', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('market_negotiation_id')->unsigned();
            $table->integer('market_condition_id')->unsigned();
            $table->boolean('is_private');
            $table->timestamps();

            $table->foreign('market_negotiation_id')
                ->references('id')->on('market_negotiations');

            $table->foreign('market_condition_id')
                ->references('id')->on('market_conditions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_negotiation_condition');
    }
}
