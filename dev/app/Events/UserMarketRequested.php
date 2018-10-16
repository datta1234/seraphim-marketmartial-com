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
use Illuminate\Database\Eloquent\SoftDeletes;


class UserMarketRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels,SoftDeletes;

    public $userMarketRequest;
    private $organisation;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at','updated_at','deleted_at'];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserMarketRequest $userMarketRequest, $organisation, $message = null)
    {
        $this->userMarketRequest = $userMarketRequest;
        $this->organisation = $organisation;
    }

    /**
    * The event's broadcast name.
    *
    * @return string
    */
    public function broadcastAs()
    {
        return 'UserMarketRequested';
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
        return ["message"=>$this->organisation->getNotification(),'data'=>$this->userMarketRequest->setOrgContext($this->organisation)->preFormatted()];
    }
}
