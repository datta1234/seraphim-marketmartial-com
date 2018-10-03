<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMarketRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_market_request_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('user_market_request_group_id')->unsigned();
            $table->string('title');
            $table->string('value');
            $table->string('type');
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')->on('items');

            $table->foreign('user_market_request_group_id')
                ->references('id')->on('user_market_request_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_market_request_items');
    }
}
