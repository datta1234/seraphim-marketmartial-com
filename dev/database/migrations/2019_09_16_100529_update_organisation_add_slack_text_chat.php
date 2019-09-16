<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrganisationAddSlackTextChat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->boolean('slack_text_chat')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn('slack_text_chat');
        });
    }
}
