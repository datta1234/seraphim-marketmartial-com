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

    public function setupChunks()
    {

    	$encoded = base64_encode($this->data);
    	$total = 200;
    	$chunkedStrings = str_split($encoded,$total);
    	$this->checkSum = hash("sha256",$encoded);
    	$this->chunks = [];

    	foreach ($chunkedStrings as $index => $data) 
    	{
    		$this->chunks[] = [
    			 "checksum" 	=> $this->checkSum,
    			 "packet"		=> $index,
    			 "total"		=> $total,
		         "expires"		=> $this->expires_at,
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
            break;
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
    			$requestedChunk["timestamp"] = $time;
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
