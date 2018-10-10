<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeConfirmationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_confirmation_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('trade_confirmation_group_id')->unsigned();
            $table->string('value');
            $table->string('title');
            $table->timestamps();

            $table->foreign('item_id')
                    ->references('id')->on('items');

            $table->foreign('trade_confirmation_group_id')
                    ->references('id')->on('trade_confirmation_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_confirmation_items');
    }
}
