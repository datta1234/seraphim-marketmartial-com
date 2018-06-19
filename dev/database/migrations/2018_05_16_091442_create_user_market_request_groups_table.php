<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMarketRequestGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_market_request_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_market_request_id')->unsigned();
            $table->integer('trade_structure_group_id')->unsigned();
            $table->boolean('is_selected');
            $table->timestamps();

            $table->foreign('user_market_request_id')
                ->references('id')->on('user_market_requests');

            $table->foreign('trade_structure_group_id')
                ->references('id')->on('trade_structure_groups');
        });

        Schema::table('user_market_request_tradables', function (Blueprint $table) {
            $table->integer('user_market_request_group_id')->unsigned()->nullable();

            $table->foreign('user_market_request_group_id', 'umrt_umrg_fk')
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
        Schema::table('user_market_request_tradables', function (Blueprint $table) {
            $table->dropForeign('umrt_umrg_fk');
        });
        Schema::table('user_market_request_tradables', function (Blueprint $table) {
            $table->dropColumn('user_market_request_group_id');
        });
        Schema::dropIfExists('user_market_request_groups');
    }
}
