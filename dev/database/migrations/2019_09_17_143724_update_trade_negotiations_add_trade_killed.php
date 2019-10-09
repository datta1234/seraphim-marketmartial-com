<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeNegotiationsAddTradeKilled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_negotiations', function (Blueprint $table) {
            $table->boolean('trade_killed')->default(false);
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
            $table->dropColumn('trade_killed');
        });
    }
}
