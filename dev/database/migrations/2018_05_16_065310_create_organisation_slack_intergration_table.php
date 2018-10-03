<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganisationSlackIntergrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisation_slack_intergration', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organisation_id')->unsigned();
            $table->integer('slack_integration_id')->unsigned();
            $table->timestamps();

            $table->foreign('organisation_id')
                ->references('id')->on('organisations');

            $table->foreign('slack_integration_id')
                ->references('id')->on('slack_integrations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organisation_slack_intergration');
    }
}
