<?php

use Illuminate\Database\Seeder;

class DefaultLabelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('default_label')->insert([
            [
                'id' => 1,
                'title' => 'Direct'
            ],
            [
                'id' => 2,
                'title' => 'Group'
            ],
            [
                'id' => 3,
                'title' => 'Clearer'
            ],
            [
                'id' => 4,
                'title' => 'Compliance'
            ],
            [
                'id' => 5,
                'title' => 'Invoices'
            ]
        ]);
    }
}
