<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MarketNegotiationTimeout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $marketNegotiationID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($negotiation)
    {
        $this->connection = config('queue.timeout');
        $this->queue = 'timeout';
        $this->marketNegotiationID = $negotiation->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo "ID: ".$this->marketNegotiationID;
        $marketNegotiation = \App\Models\Market\MarketNegotiation::find($this->marketNegotiationID);
        $userMarket = $marketNegotiation->userMarket;
        // still active = hasn't been killed AND is still the current negotiation on the user market
        $stillActive = !$marketNegotiation->is_killed 
                    && $this->marketNegotiationID === $userMarket->currentMarketNegotiation->id;
        if($stillActive) {
            
            // FoK
            if($marketNegotiation->isFoK()){
                // kill it
                $marketNegotiation->kill();
                if($marketNegotiation->cond_fok_spin == false) {
                    // @TODO: Notify Admin of No Activity
                }
            }

            // Trade @ best
            if($marketNegotiation->isTradeAtBestOpen()) {
                // kill it
                $marketNegotiation->kill();
                if($marketNegotiation->cond_fok_spin == false) {
                    // @TODO: Notify Admin of No Activity
                }
            }

            $userMarket->userMarketRequest->notifyRequested();
            echo 'killed';
        }
        return true;
    }
}
