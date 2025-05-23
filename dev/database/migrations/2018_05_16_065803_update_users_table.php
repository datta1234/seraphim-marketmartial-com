<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table){
            $table->dropColumn(['name']);

            $table->integer('role_id')->unsigned();
            $table->integer('organisation_id')->nullable()->unsigned();
            
            $table->string('full_name');
            $table->string('cell_phone');
            $table->string('work_phone');
            $table->boolean('active');
            $table->boolean('tc_accepted');
            $table->date('birthdate')->nullable();
            $table->boolean('is_married')->nullable();
            $table->boolean('has_children')->nullable();
            $table->date('last_login')->nullable();
            $table->text('hobbies')->nullable();
            
            $table->softDeletes();

            $table->foreign('role_id')
                ->references('id')->on('roles');

            $table->foreign('organisation_id')
                ->references('id')->on('organisations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table){
            $table->string('name');

            $table->dropForeign(['role_id']);
            $table->dropForeign(['organisation_id']);

            $table->dropColumn([
                'role_id', 
                'full_name',
                'cell_phone',
                'work_phone',
                'active',
                'tc_accepted',
                'birthdate',
                'is_married',
                'has_children',
                'hobbies',
                'last_login'
            ]);
            $table->dropSoftDeletes();
        });
    }
}
