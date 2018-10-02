<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_interests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('market_name');
            $table->string('contract');
            $table->date('expiry_date');
            $table->boolean('is_put');
            $table->double('open_interest', 11, 2);
            $table->double('strike_price', 11, 2);
            $table->double('delta', 11, 4);
            $table->double('spot_price', 11, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('open_interests');
    }
}
