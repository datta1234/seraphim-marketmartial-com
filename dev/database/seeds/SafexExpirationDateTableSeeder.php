<?php

use Seeders\CSVSeeder;

class SafexExpirationDateTableSeeder extends CSVSeeder
{
    protected $class = App\Models\StructureItems\SafexExpirationDate::class;
    protected $fileName = __DIR__."/data/safex_expiry_dates.csv";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->loadCSV();
    }
}
