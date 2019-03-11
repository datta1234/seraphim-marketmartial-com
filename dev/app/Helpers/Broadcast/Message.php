<?php
namespace App\Helpers\Broadcast;
use Illuminate\Support\Facades\Cache;

class Message
{
    public $data;
    public $type; 
    public $read;
    public $key;
    public $organisationId;
    public $timer;

    function __construct($organisationId, $key, $data, $status, $timer) 
    {
       $this->organisationId = $organisationId;
       $this->key = $key;
	   $this->data = $data;
       $this->status = $status;
       $this->read = false;
       $this->timer = $timer; // Used on the front to specify auto dismiss timer
       $this->storeMessage();
    }
	
    private function storeMessage()
    {
        $expiresAt = now()->addMinutes(5);
        Cache::put('organisationNotification'.$this->organisationId,$this->toArray(),$expiresAt);
    }

    public function toArray()
    {
        return [
            "key"    => $this->key,
            "data"   => $this->data,
            "status" => $this->status,
            "read"   => $this->read,
            "timer"  => $this->timer,
        ];
    }

    public static function getNotification($organisationId)
    {
        return Cache::pull('organisationNotification'.$organisationId,null);
    }
    
}