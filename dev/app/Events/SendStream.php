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
    public $total;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($broadcastName,$channel,$data,$total)
    {
        $this->broadcastName = $broadcastName;
        $this->channel = $channel;
        $this->data =   $data;
        $this->total =   $total;

    }

     

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::info(
            [$this->broadcastName,
            $this->channel,
            $this->data,
            $this->total]
        );
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
        $this->data['total'] = $this->total;
        $this->data['timestamp'] = now()->toIso8601String();
        return $this->data;
    }
}
