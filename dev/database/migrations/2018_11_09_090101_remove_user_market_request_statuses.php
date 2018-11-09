<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserMarketRequestStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_market_requests', function (Blueprint $table) {
            $table->dropForeign(['user_market_request_statuses_id']);
            $table->dropColumn('user_market_request_statuses_id');
        });
        Schema::dropIfExists('user_market_request_statuses');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('user_market_request_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });
        DB::table('user_market_request_statuses')->insert([
            'id'=>1,
            'title'=>'Created'
        ]);
        Schema::table('user_market_requests', function (Blueprint $table) {
            $table->integer('user_market_request_statuses_id')->unsigned()->default(1);
            $table->foreign('user_market_request_statuses_id')
                ->references('id')->on('user_market_request_statuses');
        });
    }
}
