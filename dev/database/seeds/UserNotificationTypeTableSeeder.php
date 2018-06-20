<?php

use Illuminate\Database\Seeder;

class UserNotificationTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            DB::table('user_notification_types')->insert([
            [
				'title'=> 'important',
            ],
            [
				'title'=> 'alert',
            ],
            [
				'title'=> 'confirmation',
            ]
        ]);
    }
}
