<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketTypesTradeStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_types_trade_structure', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('market_type_id')->unsigned();
            $table->integer('trade_structure_id')->unsigned();

            $table->foreign('market_type_id')
                ->references('id')->on('market_types');

            $table->foreign('trade_structure_id')
                ->references('id')->on('trade_structures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_types_trade_structure');
    }
}
