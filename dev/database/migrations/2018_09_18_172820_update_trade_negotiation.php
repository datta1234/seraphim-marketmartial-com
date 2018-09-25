<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeNegotiation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table('trade_negotiations', function (Blueprint $table) {
                //trade_negotiation_status_id
                $table->dropForeign(['trade_negotiation_status_id']);
                $table->dropColumn('trade_negotiation_status_id');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('trade_negotiations', function (Blueprint $table) {
                //trade_negotiation_status_id
                $table->integer('trade_negotiation_status_id')->unsigned();
                $table->foreign('trade_negotiation_status_id')
                    ->references('id')->on('trade_negotiation_statuses');
            });
    }
}
