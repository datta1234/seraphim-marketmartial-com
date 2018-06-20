<?php
namespace Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CSVSeeder extends Seeder{

    protected $class = null;
    protected $fileName = null;
    protected $total = null;
    protected $limit = 10000;
    protected $batch_size = 100;
    protected $batch = [];

    private $fields;
    private $columns;
    private $done = 0;

    public function __construct() {
        $this->fields = (new $this->class)->fillable;
        $this->dates = (new $this->class)->dates;

    }
    
    public function loadCSV()
    {

        if (file_exists($this->fileName) && $file = fopen($this->fileName, "r")) {
            $this->columns = $this->cleanExtract(fgets($file));
            while(!feof($file)) {
                $line = $this->cleanExtract(fgets($file));
                $line = $this->mutateFields($line);
                if($line !== false) {
                    $this->addToBatch($line);
                }
                // dd($this->batch,sizeof($this->batch) >= $this->batch_size || $line == false);

                if(sizeof($this->batch) >= $this->batch_size || $line == false) {

                    $saved = $this->saveRecords();
                    if(!$saved) {
                        dd("FAILED TO PARSE ROW: ", $line, $this->columns);
                    } else {
                        $this->done += sizeof($this->batch);
                        $this->batch = [];
                        echo "Saved: ".$this->done.( $this->total == null ? '' : " ( ".round(($this->done/$this->total)*100, 2)."% )" )."\r";
                    }
                }

                if($this->limit != null && $this->done >= $this->limit) {
                    echo "\nStopping at max threshold <".$this->limit."> !";
                    break;
                }
            }
            if(sizeof($this->batch) > 0) {

                    $saved = $this->saveRecords();
                    if(!$saved) {
                        dd("FAILED TO PARSE ROW: ", $line, $this->columns);
                    } else {
                        $this->done += sizeof($this->batch);
                        $this->batch = [];
                        echo "Saved: ".$this->done.( $this->total == null ? '' : " ( ".round(($this->done/$this->total)*100, 2)."% )" )."\r";
                    }
                }
            echo "\n";
            fclose($file);

        }

    }

    protected function cleanExtract($data) {
        return array_map(function($d){ 
            $d = trim($d); 
            if($d == "\N") {
                $d = null;
            }
            return $d;
        }, explode(",", $data));
    }

    protected function addToBatch($data) {
        $this->batch[] = $data;
    }

    protected function mutateFields($data) {
        $new = [];
        if(sizeof($data) != sizeof($this->columns)) {
            return false;
        }
        
        if(empty($data) || $data == null)
        {
            return false;
        }

        $data = array_combine($this->columns, $data);
        foreach($this->fields as $field) {

            if($this->dates && in_array($field, $this->dates))
            {
                $date = new Carbon($data[$field]);
                $new[$field] = $date->format('Y-m-d H:i:s');
            }else
            {
                $new[$field] = $data[$field];
            }
        }
        return $new;
    }

    protected function saveRecords() {
        return $this->class::insert($this->batch);
    }

}