<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDerivativesUserManagementForeignKeyAdditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trading_accounts', function (Blueprint $table){
            $table->foreign('derivative_id')
                ->references('id')->on('derivatives');
        });

        Schema::table('user_derivative_watched', function (Blueprint $table){
            $table->foreign('derivative_id')
                ->references('id')->on('derivatives');
        });

        Schema::table('user_derivative_interests', function (Blueprint $table){
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
        Schema::table('trading_accounts', function (Blueprint $table){
            $table->dropForeign(['derivative_id']);
        });

        Schema::table('user_derivative_watched', function (Blueprint $table){
            $table->dropForeign(['derivative_id']);
        }); 

        Schema::table('user_derivative_interests', function (Blueprint $table){
            $table->dropForeign(['derivative_id']);
        });  
    }
}
