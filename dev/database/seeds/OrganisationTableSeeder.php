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
        $orgs = 1;
        factory(App\Models\UserManagement\Organisation::class,8)->create()->each(function($organisation){
               // dd($organisation);
    	        factory(App\Models\UserManagement\User::class, 10)->create([
					'organisation_id' =>  $organisation->id,
    	        ]);
                factory(App\Models\UserManagement\User::class)->create([
                    'email' =>  'org'.$orgs.'@example.net',
                    'organisation_id' =>  $organisation->id,
                ]);
                $orgs++;
        });
    }
}
