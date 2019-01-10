<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStocksCodeSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('code', 10)->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::update('UPDATE `stocks` SET `code`=SUBSTRING(`code` FROM 1 FOR 3) WHERE 1');

        Schema::table('stocks', function (Blueprint $table) {
            $table->string('code', 3)->change();
        });
    }
}
