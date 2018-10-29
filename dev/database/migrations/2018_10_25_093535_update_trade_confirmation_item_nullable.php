<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeConfirmationItemNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_confirmation_items', function (Blueprint $table) {
            $table->string('value')->nullable()->change();

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
            // $table->string('value')->nullable(false)->change(); // since they'll be null coloumns theres no going back
        });
    }
}
