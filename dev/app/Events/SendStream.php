<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendStream implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $broadcastName;
    public $channel;
    public $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($broadcastName,$channel,$data)
    {
        $this->broadcastName = $broadcastName;
        $this->channel = $channel;
        $this->data =   $data;
    }

     

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return $this->channel;
    }

    public function broadcastAs()
    {
        return  $this->broadcastName;
    }

     /**
    * Get the data to broadcast.
    *
    * @return array
    */
    public function broadcastWith()
    {
        $this->data['time'] = now()->format("d-m-y H:i:s");
        return $this->data;
    }
}
