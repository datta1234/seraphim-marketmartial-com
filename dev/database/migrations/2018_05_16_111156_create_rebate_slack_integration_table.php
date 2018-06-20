<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRebateSlackIntegrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rebate_slack_integration', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('slack_integration_id')->unsigned();
            $table->integer('rebate_id')->unsigned();
            $table->timestamps();

            $table->foreign('slack_integration_id')
                ->references('id')->on('slack_integrations');

            $table->foreign('rebate_id')
                ->references('id')->on('rebates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rebate_slack_integration');
    }
}
