<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketJseIntergrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_jse_intergration', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jse_intergration_id')->unsigned();
            $table->integer('market_id')->unsigned();
            $table->timestamps();

            $table->foreign('jse_intergration_id')
                ->references('id')->on('jse_intergrations');
                
            $table->foreign('market_id')
                ->references('id')->on('markets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_jse_intergration');
    }
}
