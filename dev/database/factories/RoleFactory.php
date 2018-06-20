<?php

use Faker\Generator as Faker;

$roles = config('marketmartial.roles');

foreach ($roles as $role) 
{
	$factory->defineAs(App\Models\UserManagement\Role::class,$role['title'],function (Faker $faker) use ($role){
	    return [
	        'title' => $role['title'],
	        'is_selectable' => $role['is_selectable']
	    ];
	});

}
