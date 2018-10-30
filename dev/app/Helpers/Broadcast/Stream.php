<?php
namespace App\Helpers\Broadcast;
use App\Events\SendStream;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Stream
{
    public $broadcastName;
    public $channel;
    public $data;
    public $chunks = [];
    public $experation;
    public $checkSum;
    private $overHead = [];

    function __construct($event) 
    {
        $this->broadcastName = $event->broadcastAs();
        $this->channel = $event->broadcastOn();
        $this->data =   json_encode($event->broadcastWith());
        $this->expires_at = Carbon::now()->addMinutes(config('marketmartial.stream_settings.expiration'));
        $this->setupChunks();
        $this->storeData();
    }

    public function getChunkAtPointer($str,$pointer,$prev_limit_bytes,$limit_in_bytes)
    {
        return substr($str,($pointer * $prev_limit_bytes),$limit_in_bytes);
    }


    public function setupChunks()
    {

        $encoded = base64_encode($this->data);
        //$chunkedStrings = $this->parseStrToArr($encoded,7000);
        $limit = config('marketmartial.stream_settings.limit') * 1000;
        $pointer = 0;
        $prev_limit_bytes = 0;
        $compiledData = '';
        $this->checkSum = hash("sha256",$encoded.$this->expires_at->timestamp);
        $this->chunks = [];

        while($encoded != $compiledData)
        {
            $chunk = [
                 "checksum"     => $this->checkSum,
                 "packet"       => $pointer + 1,
                 "expires"      => $this->expires_at->toIso8601String(),
            ];


            $usedBytes = $this->calcUsedOverHead($chunk);
            $limit_in_bytes = $limit - $usedBytes;
            $chunk["data"]  = $this->getChunkAtPointer($encoded,$pointer,$prev_limit_bytes, $limit_in_bytes);
           $compiledData   = $compiledData .$chunk["data"];
            $this->chunks[] = $chunk; 
            $prev_limit_bytes = $limit_in_bytes;
            $pointer++;
        }
        $this->total = count($this->chunks);
      
    }

    private function calcUsedOverHead($chunk)
    {
        $chunk['time'] = "dd-mm-yy hh:ii:ss";//placeholder for the time 
        $chunk['total'] = "NNNN";//placeholder total
        return strlen(json_encode($chunk));
    }

    public function storeData()
    {
        Cache::put('streamData_'.$this->checkSum,$this->chunks,$this->expires_at);  
    }


    public static function hasChecksum($checkSum)
    {
        return Cache::has('streamData_'.$checkSum);
    }

    public function run($idx = null)
    {
        \Log::info([$this->broadcastName,$this->channel,$this->chunks,$this->total]);

        foreach ($this->chunks as $k => $chunk) 
        {
            event(new SendStream($this->broadcastName,$this->channel,$chunk,$this->total));
        }
    }

    public static function getChunks($checkSum,$indexes = [])
    {
        $chunks = Cache::get('streamData_'.$checkSum,[]);
        $total = count($chunks);

        $hasKeys = empty($indexes); 
        $requestedChunks = [];
        $time = Carbon::now();
        
        if(!empty($chunks))
        {
           $chunks = array_where($chunks, function ($chunk, $key) use($indexes) {
                return in_array($chunk['packet'], $indexes);
            });  
        }
        foreach ($chunks as $chunk) 
        {
            $chunk['timestamp'] = $time;
            $chunk['total'] = $total;
        }
            
        return array_values($chunks); 
    }


}
