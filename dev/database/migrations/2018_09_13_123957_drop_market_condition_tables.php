<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropMarketConditionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // unhook foreign keys
        Schema::table('market_conditions', function (Blueprint $table) {
            $table->dropForeign(['market_condition_category_id']);
        });
        Schema::table('market_condition_categories', function (Blueprint $table) {
            $table->dropForeign(['market_condition_category_id']);
        });

        // drop tables
        Schema::dropIfExists('market_conditions');
        Schema::dropIfExists('market_condition_categories');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('market_condition_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('market_condition_category_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('market_condition_category_id')
                ->references('id')->on('market_condition_categories');
        });
        Schema::create('market_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('alias');
            $table->integer('market_condition_category_id')->nullable()->unsigned();
            $table->integer('timeout');
            $table->timestamps();

            $table->foreign('market_condition_category_id')
                ->references('id')->on('market_condition_categories');
        });
    }
}
