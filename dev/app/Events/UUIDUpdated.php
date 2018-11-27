<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UUIDUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $old_id;
    private $organisation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($organisation, $old_id)
    {
        $this->old_id = $old_id;
        $this->organisation = $organisation;
    }

    /**
    * The event's broadcast name.
    *
    * @return string
    */
    public function broadcastAs()
    {
        return 'UUIDUpdated';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('organisation.'.$this->old_id);    
    }

    /**
    * Get the data to broadcast.
    *
    * @return array
    */
    public function broadcastWith()
    {
        return ["message"=>"Client Identity Updated",'data'=>$this->organisation->uuid];
    }
}
