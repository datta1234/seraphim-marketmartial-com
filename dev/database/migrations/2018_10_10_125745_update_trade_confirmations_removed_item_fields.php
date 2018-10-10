<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeConfirmationsRemovedItemFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->dropColumn([
                'spot_price',
                'future_reference',
                'near_expiery_reference',
                'puts',
                'calls',
                'delta',
                'contracts',
                'buy_gross_premiums',
                'buy_net_premiums',
                'sell_gross_premiums',
                'sell_net_premiums',
                'is_confirmed'
            ]);
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
            $table->double('spot_price', 11, 2)->nullable();
            $table->double('future_reference', 11, 2)->nullable();
            $table->double('near_expiery_reference', 11, 2)->nullable();
            $table->double('puts', 11, 2)->nullable();
            $table->double('calls', 11, 2)->nullable();
            $table->double('delta', 11, 2)->nullable();
            $table->double('contracts', 11, 2);
            
            $table->double('buy_gross_premiums', 11, 2);
            $table->double('buy_net_premiums', 11, 2);
            $table->double('sell_gross_premiums', 11, 2);
            $table->double('sell_net_premiums', 11, 2);
            
            $table->boolean('is_confirmed');

        });
    }
}
