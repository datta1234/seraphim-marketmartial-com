<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Trade\Rebate;
use App\Models\UserManagement\Organisation;

class TradeConfirmationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rebate;
    private $organisation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Rebate $rebate,Organisation $organisation)
    {
        $this->rebate = $rebate;
        $this->organisation = $organisation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('organisation.'.$this->organisation->uuid);
    }

    /**
    * The event's broadcast name.
    *
    * @return string
    */
    public function broadcastAs()
    {
        return 'Rebate Event';
    }

    /**
    * Get the data to broadcast.
    *
    * @return array
    */
    public function broadcastWith()
    {
        return ["message"=>$this->organisation->getNotification(),'data'=>$this->rebate->setOrgContext($this->organisation)->preFormatted()];
    }
}
