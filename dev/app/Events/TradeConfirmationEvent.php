<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\TradeConfirmations\TradeConfirmation;
use App\Models\UserManagement\Organisation;

class TradeConfirmationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tradeconfirmation;
    private $organisation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TradeConfirmation $tradeconfirmation,Organisation $organisation)
    {
        $this->tradeconfirmation = $tradeconfirmation;
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
        return 'TradeConfirmationEvent';
    }

    /**
    * Get the data to broadcast.
    *
    * @return array
    */
    public function broadcastWith()
    {
        $this->tradeconfirmation->load(['futureGroups','optionGroups']);
        
        return ["message"=>$this->organisation->getNotification(),'data'=>$this->tradeconfirmation->setOrgContext($this->organisation)->preFormatted()];
    }
}
