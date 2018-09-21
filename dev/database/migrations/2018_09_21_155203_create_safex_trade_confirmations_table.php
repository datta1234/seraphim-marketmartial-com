<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSafexTradeConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('safex_trade_confirmations', function (Blueprint $table) {
            $table->increments('id');
            $table->double('trade_id', 11, 3);
            $table->string('underlying');
            $table->date('trade_date');
            $table->string('structure');
            $table->string('underlying_alt');
            $table->double('strike', 11, 2);
            $table->double('strike_percentage', 11, 2)->nullable();
            $table->boolean('is_put');
            $table->string('volspread');
            $table->date('expiry');
            $table->double('nominal', 11, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('safex_trade_confirmations');
    }
}
