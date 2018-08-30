<?php
namespace App\Helpers\Broadcast;
use App\Events\SendStream;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Stream
{
	public $id;
	public $broadcastName;
	public $channel;
	public $data;
	public $chunks = [];
	public $experation;
	public $checkSum;

	function __construct($event) 
    {
    	$this->broadcastName = $event->broadcastAs();
    	$this->channel = $event->broadcastOn();
    	$this->data =   json_encode($event->broadcastWith());
    	$this->expires_at = Carbon::now();
    	
    	$this->setupChunks();
    	$this->storeData();
    }


    public function parseStrToArr($string, $limit_in_bytes){
        $ret = [];

        $pointer = 0;
        while(strlen($string) > (($pointer + 1) * $limit_in_bytes)){
            $ret[] = substr($string, ($pointer * $limit_in_bytes ), $limit_in_bytes);
            $pointer++;
        }

        $ret[] = substr($string, ($pointer * $limit_in_bytes), $limit_in_bytes);

        return $ret;
    }


    public function setupChunks()
    {

    	$encoded = base64_encode($this->data);
    	$chunkedStrings = $this->parseStrToArr($encoded,7000);
        $total = count($chunkedStrings);

    	$this->checkSum = hash("sha256",$encoded);
    	$this->chunks = [];

    	foreach ($chunkedStrings as $index => $data) 
    	{
    		$this->chunks[] = [
    			 "checksum" 	=> $this->checkSum,
    			 "packet"		=> $index + 1,
    			 "total"		=> $total,
		         "expires"		=> $this->expires_at->format("d-m-y H:i:s"),
		         "data"			=> $data
    		];
    	}
    }

    public function storeData()
    {
   		 Cache::put('streamData'.$this->id,$this->chunks,$this->expires_at);	
    }

    public function run()
    {
    	foreach ($this->chunks as $chunk) 
    	{
    		event(new SendStream($this->broadcastName,$this->channel,$chunk));
    	}
    }

    public function getChunks($indexes = [])
    {
    	$chunks = Cache::get('streamData'.$this->id,[]);
    	$hasKeys = empty($indexes);	
    	$requestedChunks = [];
    	$time = Carbon::now();
    	
    	foreach ($chunks as $index => $chunk) 
    	{
    		if(!$hasKeys || ($hasKeys && array_key_exists($index, $indexes)))
    		{
    			$requestedChunk = [];
    			$requestedChunk["timestamp"] = $time->format("d-m-y H:i:s");
    			$requestedChunk = array_merge($chunk,$requestedChunk);
    			$requestedChunks[] = $requestedChunk;
    		}

    		if($index === count(($indexes)))//stop the loop so we dont do unneccesary loops
    		{
    			break;
    		}
    	}
    	return $requestedChunks;	
    }


}
