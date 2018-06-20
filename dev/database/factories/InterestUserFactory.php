<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\InterestUser::class, function (Faker $faker) {
    return [
        'value' => $faker->word,
        'interest_id'=> function(){
            return factory(App\Models\UserManagement\Interest::class)->create()->id;
        },
        'user_id'=> function(){
            return factory(App\Models\UserManagement\User::class)->create()->id;
        },
    ];
});
