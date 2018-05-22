<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('is_seldom');
            $table->integer('market_type_id')->unsigned();
            $table->text('description')->nullable();
            $table->boolean('has_deadline');
            $table->boolean('has_negotiation');
            $table->boolean('needs_spot');
            $table->boolean('has_rebate');
            $table->timestamps();

            $table->foreign('market_type_id')
                ->references('id')->on('market_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('markets');
    }
}
