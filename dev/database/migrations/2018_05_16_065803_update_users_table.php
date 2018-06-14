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

            // @defaults
            $table->integer('role_id')->default(3)->unsigned();
            $table->integer('organisation_id')->nullable()->unsigned();
            
            // @defaults
            $table->string('full_name')->nullable();
            // @defaults
            $table->string('cell_phone')->nullable();
            $table->string('work_phone')->nullable();
            // @defaults
            $table->boolean('active')->default(0);
            // @defaults
            $table->boolean('tc_accepted')->default(0);
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
