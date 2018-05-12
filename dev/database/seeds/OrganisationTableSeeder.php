<?php

use Illuminate\Database\Seeder;

class OrganisationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\UserManagement\Organisation::class,8)->create()->each(function($organisation){
    	        factory(App\User::class, 10)->create([
					'organisation_id' =>  $organisation->id,
    	        ]);
        });
    }
}
