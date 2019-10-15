<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMarketNegotiationsAddCondExpiry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_negotiations', function (Blueprint $table) {
            $table->timestamp('cond_expiry')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('market_negotiations', function (Blueprint $table) {
            $table->dropColumn('cond_expiry');
        });
    }
}
