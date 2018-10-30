<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TradeConfirmationItemsAddSeller extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_confirmation_items', function (Blueprint $table) {
            $table->boolean('is_seller')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_confirmation_items', function (Blueprint $table) {
                $table->dropColumn('is_seller');
        });
    }
}
