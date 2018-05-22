<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'title' => 'Admin',
                'is_selectable' => false,
            ],
            [
                'id' => 2,
                'title' => 'Trader',
                'is_selectable' => true,
            ],
            [
                'id' => 3,
                'title' => 'Viewer',
                'is_selectable' => true,
            ]
        ]);
    }
}
