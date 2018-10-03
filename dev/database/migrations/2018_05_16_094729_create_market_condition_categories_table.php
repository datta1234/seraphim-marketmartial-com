<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketConditionCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_condition_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('market_condition_category_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('market_condition_category_id')
                ->references('id')->on('market_condition_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_condition_categories');
    }
}
