<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserManagement\UserNotification::class, function (Faker $faker) {
    return [
			"user_id" => factory(App\Models\UserManagement\User::class)->create()->id,
			"notifiable_id" => rand(0,1) == 1,
			"user_notification_type_id" => factory(App\Models\UserManagement\UserNotificationType::class)->create()->id,
			"data" => $faker->sentence,
			"description" => $faker->word,
			"type" => $faker->word,
			"notifiable_type" => $faker->word,
			"read_at" => $faker->date()
    ];
});
