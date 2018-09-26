<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMarketNegotiationIsPrivateDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_negotiations', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->change();
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
            $table->boolean('is_private')->default(true)->change();
        });
    }
}
