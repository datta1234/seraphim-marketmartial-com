<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeConfirmationGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_confirmation_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trade_confirmation_id')->unsigned();
            $table->integer('trade_structure_group_id')->unsigned();
            $table->integer('user_market_request_group_id')->unsigned();
            $table->integer('trade_confirmation_group')->nullable()->unsigned();
            $table->boolean('is_options');
            $table->timestamps();

            $table->foreign('trade_confirmation_id')
                    ->references('id')->on('trade_confirmations');

            $table->foreign('trade_structure_group_id')
                    ->references('id')->on('trade_structure_groups');

            $table->foreign('user_market_request_group_id')
                    ->references('id')->on('user_market_request_groups');

            $table->foreign('trade_confirmation_group')
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
        Schema::dropIfExists('trade_confirmation_groups');
    }
}
