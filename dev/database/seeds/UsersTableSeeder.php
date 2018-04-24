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
                'name' => "admin",
                'email' => "staging@assemble.co.za",
                'password' => bcrypt('jM68b7LdRspcHhMv'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
        ]);
    }
}
