<?php
use Carbon\Carbon;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\UserManagement\User::class, function (Faker $faker) {

    return [
			'full_name' => $faker->name,
			'email' => $faker->unique()->safeEmail,
			'cell_phone' => $faker->e164PhoneNumber,
			'work_phone' => $faker->e164PhoneNumber,
			'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
			'remember_token' => str_random(10),
			'active' => false,
			'verified' => false,
			'tc_accepted' => true,
			'role_id' =>  function(){
				$role = App\Models\UserManagement\Role::where('title',config("marketmartial.default_role"))->first();
				return is_null($role) ?  factory(App\Models\UserManagement\Role::class,config("marketmartial.default_role"))->create()->id : $role->id;
			},
			'birthdate' => $faker->dateTimeBetween('-65 year', '-21 year'),
			'is_married' => rand(0,1) == 1,
			'has_children' => rand(0,1) == 1,
			'last_login'=> null,
			'organisation_id' =>  function(){
				$organisations = App\Models\UserManagement\Organisation::all();

					if($organisations->count() == 0)
					{
						return  factory(App\Models\UserManagement\Organisation::class)->create()->id;
					}else
					{
						return $organisations->random()->id;
					}

				},
			'hobbies'=> $faker->sentence() 
    ];
});
