<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeStructureGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_structure_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('trade_structure_id')->unsigned();
            $table->timestamps();

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
        Schema::dropIfExists('trade_structure_groups');
    }
}
