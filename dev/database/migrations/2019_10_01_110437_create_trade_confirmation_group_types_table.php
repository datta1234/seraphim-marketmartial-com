<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeConfirmationGroupTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_confirmation_group_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });

        Schema::table('trade_confirmation_groups', function (Blueprint $table){
            $table->integer('trade_confirmation_group_type_id')->unsigned()->nullable();

            $table->foreign('trade_confirmation_group_type_id', 'tc_groups_tc_group_type_id_foreign')
            ->references('id')->on('trade_confirmation_group_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select('ALTER TABLE `trade_confirmation_groups` 
            DROP FOREIGN KEY 
            `tc_groups_tc_group_type_id_foreign`'
        );
        DB::select('ALTER TABLE `trade_confirmation_groups` 
            DROP INDEX
            `tc_groups_tc_group_type_id_foreign`'
        );

        Schema::table('trade_confirmation_groups', function (Blueprint $table){
            $table->dropColumn(['trade_confirmation_group_type_id']);
        });

        Schema::dropIfExists('trade_confirmation_group_types');
    }
}
