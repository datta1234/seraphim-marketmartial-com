<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTradeConfirmationsAddingSendingReceivingAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->dropForeign(['trading_account_id']);
            $table->dropColumn(['trading_account_id']);

            $table->integer('send_trading_account_id')->nullable()->unsigned();
            $table->integer('receiving_trading_account_id')->nullable()->unsigned();

            $table->foreign('send_trading_account_id')
                ->references('id')->on('trading_accounts');
            $table->foreign('receiving_trading_account_id')
                ->references('id')->on('trading_accounts');      
            
        });
        
        self::seedTradeConfirmationTradingAccounts(true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_confirmations', function (Blueprint $table) {
            $table->dropForeign(['send_trading_account_id']);
            $table->dropForeign(['receiving_trading_account_id']);
            $table->dropColumn(['send_trading_account_id']);
            $table->dropColumn(['receiving_trading_account_id']);
            
            $table->integer('trading_account_id')->nullable()->unsigned();

            $table->foreign('trading_account_id')
                ->references('id')->on('trading_accounts');
            
        });
        
        self::seedTradeConfirmationTradingAccounts(false);
    }

    private static function seedTradeConfirmationTradingAccounts($is_up)
    {
        if($is_up) {
            DB::select('UPDATE `trade_confirmations` tc 
                SET `send_trading_account_id`= (
                    SELECT ta.`id`
                    FROM `trading_accounts` ta
                    WHERE ta.`market_id` = tc.`market_id`
                    AND ta.`user_id` = tc.`send_user_id`
                    LIMIT 1
                ), 
                `receiving_trading_account_id`= (
                    SELECT ta.`id`
                    FROM `trading_accounts` ta
                    WHERE ta.`market_id` = tc.`market_id`
                    AND ta.`user_id` = tc.`receiving_user_id`
                    LIMIT 1
                )
                WHERE 1'
            );
        } else {
            DB::select('UPDATE `trade_confirmations` tc 
                SET `trading_account_id`= (
                    SELECT ta.`id`
                    FROM `trading_accounts` ta
                    WHERE ta.`market_id` = tc.`market_id`
                    AND ta.`user_id` = tc.`send_user_id`
                    LIMIT 1
                )
                WHERE 1'
            );
        }
    }
}
