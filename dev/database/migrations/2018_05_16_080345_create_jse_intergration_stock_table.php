<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJseIntergrationStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jse_intergration_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('jse_intergration_id')->unsigned();
            $table->integer('stock_id')->unsigned();
            $table->timestamps();

            $table->foreign('jse_intergration_id')
                ->references('id')->on('jse_intergrations');
                
            $table->foreign('stock_id')
                ->references('id')->on('stocks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jse_intergration_stock');
    }
}
