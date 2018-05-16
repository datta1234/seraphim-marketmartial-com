<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('item_type_id')->unsigned();
            $table->integer('trade_structure_group_id')->unsigned();
            $table->timestamps();

            $table->foreign('item_type_id')
                ->references('id')->on('item_types');

            $table->foreign('trade_structure_group_id')
                ->references('id')->on('trade_structure_groups');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
