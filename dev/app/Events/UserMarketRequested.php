<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\MarketRequest\UserMarketRequest;
use App\Models\UserManagement\Organisation;
use Illuminate\Support\Facades\Log;

class UserMarketRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userMarketRequest;
    private $organisation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserMarketRequest $userMarketRequest, $organisation)
    {
        $this->userMarketRequest = $userMarketRequest; 
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
    * Get the data to broadcast.
    *
    * @return array
    */
    public function broadcastWith()
    {
        return $this->userMarketRequest->setOrgContext($this->organisation)->preFormatted();
    }
}
