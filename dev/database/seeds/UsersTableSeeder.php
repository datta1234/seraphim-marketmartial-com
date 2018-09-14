<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       /*
        *Default admin user
        */
        $user_id =  DB::table('users')->insertGetId([
                'full_name' => "Assemble Admin",
                'email' => "staging@assemble.co.za",
                'cell_phone' => "0844094293",
                'work_phone' => "0844094293",
                'password' => bcrypt('jM68b7LdRspcHhMv'),
                'role_id' => 1,
                'active' => true,
                'verified' => true,
                'remember_token' => null,
                'last_login' => null,
                'tc_accepted' => true,
                'is_married'=> false,
                'organisation_id'=> null,
                'birthdate' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'hobbies' => "code"
        ]);


    }
}
