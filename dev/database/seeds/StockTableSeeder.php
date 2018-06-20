<?php

use Seeders\CSVSeeder;

class StockTableSeeder extends CSVSeeder
{
    protected $class = App\Models\StructureItems\Stock::class;
    protected $fileName = __DIR__."/data/stock_codes.csv";    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
    
        $this->loadCSV();
    }
}
