<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeStructuresAddFeePercentage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_structures', function (Blueprint $table){
            $table->boolean('has_structure_fee')->default(false);
            $table->double('fee_percentage', 11, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_structures', function (Blueprint $table){
            $table->dropColumn(['fee_percentage','has_structure_fee']);
        });
    }
}
