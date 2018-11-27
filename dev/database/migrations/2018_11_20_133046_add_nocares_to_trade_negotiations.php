<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNocaresToTradeNegotiations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_negotiations', function (Blueprint $table) {
            $table->boolean('no_cares')->default(false);
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
            $table->dropColumn('no_cares');
        });
    }
}
