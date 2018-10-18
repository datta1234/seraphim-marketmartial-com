<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->dropColumn(['gross_premiums', 'net_premiums']);

            $table->double('buy_gross_premiums', 11, 2);
            $table->double('buy_net_premiums', 11, 2);
            $table->double('sell_gross_premiums', 11, 2);
            $table->double('sell_net_premiums', 11, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->dropColumn(['buy_gross_premiums',
                'buy_net_premiums',
                'sell_gross_premiums',
                'sell_net_premiums'
            ]);

            $table->double('gross_premiums', 11, 2);
            $table->double('net_premiums', 11, 2);
        });
    }
}
