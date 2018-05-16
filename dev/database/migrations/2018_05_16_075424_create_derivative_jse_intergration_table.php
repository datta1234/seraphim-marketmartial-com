<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDerivativeJseIntergrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('derivative_jse_intergration', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jse_intergration_id')->unsigned();
            $table->integer('derivative_id')->unsigned();
            $table->timestamps();

            $table->foreign('jse_intergration_id')
                ->references('id')->on('jse_intergrations');
                
            $table->foreign('derivative_id')
                ->references('id')->on('derivatives');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('derivative_jse_intergration');
    }
}
