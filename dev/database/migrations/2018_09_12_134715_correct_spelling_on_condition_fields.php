<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CorrectSpellingOnConditionFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('market_negotiations', function (Blueprint $table) {
           $table->renameColumn('cond_is_ocd', 'cond_is_oco')->nullable();
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
           $table->renameColumn('cond_is_oco', 'cond_is_ocd')->nullable();
        });
    }
}
