<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRebatesTableChangeTrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rebates', function (Blueprint $table) {
            
            $table->dropForeign(['trade_id']);
            $table->dropColumn(['trade_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rebates', function (Blueprint $table) {
            $table->integer('trade_id')->nullable()->unsigned();
            $table->foreign('trade_id')->references('id')->on('trades');

        });
    }
}
