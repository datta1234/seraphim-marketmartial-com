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
        echo "Processing Timeout For Market Negotiation ID: ".$this->marketNegotiationID.PHP_EOL;
        $marketNegotiation = \App\Models\Market\MarketNegotiation::findOrFail($this->marketNegotiationID);
        $userMarket = $marketNegotiation->userMarket;
        
        $stillActive = !$marketNegotiation->is_killed //no killed
                    && $marketNegotiation->marketNegotiationChildren()->count() == 0 ;// has no children

        // is the latest negotiation in the tree
        if($stillActive) {
            
            // FoK
            if($marketNegotiation->isFoK() && !$marketNegotiation->isTrading()){
                // kill it
                if($marketNegotiation->cond_fok_spin == false) {
                    $marketNegotiation->kill();
                    $marketNegotiation->fresh()->userMarket->userMarketRequest->notifyRequested();
                }
                if($marketNegotiation->cond_fok_spin == true) {
                    // Notify The Admin 
                    \Slack::postMessage([
                        "text"      => $marketNegotiation->getMessage('fok_timeout'),
                        "channel"   => env("SLACK_ADMIN_NOTIFY_CHANNEL")
                    ], 'timeout');
                }
            }

            // Trade @ best
            if(($marketNegotiation->isTradeAtBest() || $marketNegotiation->isTradeAtBestOpen()) && !$marketNegotiation->isTrading()) {

                $sourceNegotiation = $marketNegotiation->tradeAtBestSource();
                $tradeNegotiation = $marketNegotiation->addTradeNegotiation($sourceNegotiation->user, [
                    "quantity"      =>  $marketNegotiation->cond_buy_best ? $marketNegotiation->offer_qty : $marketNegotiation->bid_qty,
                    "is_offer"      =>  $marketNegotiation->cond_buy_best,
                    "is_distpute"   =>  false,
                ]);

                $marketNegotiation->fresh()->userMarket->userMarketRequest->notifyRequested();

                // Notify The Admin 
                $term = $marketNegotiation->cond_buy_best == true ? 'Buy' : 'Sell';
                $title_initiator = $sourceNegotiation->user->organisation->title;
                $title_responder = $marketNegotiation->user->organisation->title;
                $level = $term == 'Buy' ? $marketNegotiation->offer : $marketNegotiation->bid;
                \Slack::postMessage([
                    "text"      => "A ".$term." at Best Timeout has occured, Trading Between _".$title_initiator."_ and _".$title_responder."_ @ *".$level."*",
                    "channel"   => env("SLACK_ADMIN_NOTIFY_CHANNEL")
                ], 'timeout');
            }
        }
        return true;
    }
}
