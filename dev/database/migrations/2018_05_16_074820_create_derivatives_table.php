<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDerivativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('derivatives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('is_seldom');
            $table->integer('derivative_type_id')->unsigned();
            $table->text('description');
            $table->boolean('has_deadline');
            $table->boolean('has_negotiation');
            $table->boolean('has_rebate');
            $table->timestamps();

            $table->foreign('derivative_type_id')
                ->references('id')->on('derivative_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('derivatives');
    }
}
