<?php

use Illuminate\Database\Seeder;

class InterestTableSeeder extends Seeder
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
       DB::table('interests')->insert(
       	[
                'title' => "Football"
        ],
        [
                'title' => "Rugby"
        ],
        [
                'title' => "Cricket"
        ],
        [
                'title' => "Tennise"
        ],
        [
                'title' => "Cycling"
        ],
        [
                'title' => "F1/MotoGP"
        ],
        [
                'title' => "Golf"
        ],
        [
                'title' => "Other"
        ]
    );
    }
}
