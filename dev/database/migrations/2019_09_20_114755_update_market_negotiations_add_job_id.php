<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMarketNegotiationsAddJobId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_negotiations', function (Blueprint $table) {
            $table->bigInteger('job_id')->unsigned()->nullable();

            /*$table->foreign('job_id')
                ->references('id')->on('jobs');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('market_negotiations', function (Blueprint $table) {
            //$table->dropForeign(['job_id']);
            $table->dropColumn('job_id');
        });
    }
}
