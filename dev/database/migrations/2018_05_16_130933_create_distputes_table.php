<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distputes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('send_user_id')->unsigned();
            $table->integer('receiving_user_id')->unsigned();
            $table->integer('distpute_status_id')->unsigned();
            $table->integer('trade_confirmation_id')->unsigned();
            $table->timestamps();

            $table->foreign('send_user_id')
                ->references('id')->on('users');

            $table->foreign('receiving_user_id')
                ->references('id')->on('users');

            $table->foreign('distpute_status_id')
                ->references('id')->on('distpute_status');

            $table->foreign('trade_confirmation_id')
                ->references('id')->on('trade_confirmations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distputes');
    }
}
