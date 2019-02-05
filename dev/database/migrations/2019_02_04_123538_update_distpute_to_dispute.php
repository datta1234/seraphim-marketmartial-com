<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDistputeToDispute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trade_negotiations', function (Blueprint $table) {
            $table->renameColumn('is_distpute', 'is_dispute');
        });
        
        Schema::table('distputes', function (Blueprint $table) {
            $table->dropForeign(['distpute_status_id']);
            $table->dropForeign(['send_user_id']);
            $table->dropForeign(['receiving_user_id']);
            $table->dropForeign(['trade_confirmation_id']);
        });

        Schema::rename('distputes', 'disputes');
        Schema::rename('distpute_status', 'dispute_status');

        Schema::table('disputes', function (Blueprint $table) {
            $table->renameColumn('distpute_status_id', 'dispute_status_id');

            $table->foreign('send_user_id')
                ->references('id')->on('users');

            $table->foreign('receiving_user_id')
                ->references('id')->on('users');

            $table->foreign('dispute_status_id')
                ->references('id')->on('dispute_status');

            $table->foreign('trade_confirmation_id')
                ->references('id')->on('trade_confirmations');
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
            $table->renameColumn('is_dispute', 'is_distpute');
        });

        Schema::table('disputes', function (Blueprint $table) {
            $table->dropForeign(['dispute_status_id']);
            $table->dropForeign(['send_user_id']);
            $table->dropForeign(['receiving_user_id']);
            $table->dropForeign(['trade_confirmation_id']);
        });

        Schema::rename('disputes', 'distputes');
        Schema::rename('dispute_status', 'distpute_status');

        Schema::table('distputes', function (Blueprint $table) {
            $table->renameColumn('dispute_status_id', 'distpute_status_id');

            $table->foreign('send_user_id')
                ->references('id')->on('users');

            $table->foreign('receiving_user_id')
                ->references('id')->on('users');

            $table->foreign('distpute_status_id')
                ->references('id')->on('distpute_status');

            $table->foreign('trade_confirmation_id')
                ->references('id')->on('trade_confirmations');
        });
    }
}
