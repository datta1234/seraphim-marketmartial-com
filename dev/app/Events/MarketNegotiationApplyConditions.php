<?php

namespace App\Events\Models;

use Illuminate\Queue\SerializesModels;
use App\Models\Market\MarketNegotiation;

class MarketNegotiationApplyConditions
{
    use SerializesModels;

    public $negotiation;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MarketNegotiation $negotiation)
    {
        $this->negotiation = $negotiation;
    }
}
