<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeStructureGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_structure_groups', function (Blueprint $table) {
            $table->integer('trade_structure_group_type_id')->nullable()->unsigned();
            
            $table->foreign('trade_structure_group_type_id')
                    ->references('id')->on('trade_structure_group_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_structure_groups', function (Blueprint $table) {
            $table->dropForeign(['trade_structure_group_type_id']);
            $table->dropColumn(['trade_structure_group_type_id']);
        });

    }
}
