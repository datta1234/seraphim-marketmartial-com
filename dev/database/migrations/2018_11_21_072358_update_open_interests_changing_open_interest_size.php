<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOpenInterestsChangingOpenInterestSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `open_interests` 
            CHANGE `open_interest` 
            `open_interest` DOUBLE(20,2) 
            NOT NULL'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select('ALTER TABLE `open_interests` 
            CHANGE `open_interest` 
            `open_interest` DOUBLE(11,2) 
            NOT NULL'
        );
    }
}
