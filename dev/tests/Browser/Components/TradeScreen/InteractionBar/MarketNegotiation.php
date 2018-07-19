<?php

namespace Tests\Browser\Components\TradeScreen\InteractionBar;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class MarketNegotiation extends BaseComponent
{
    public function __construct($type, &$data, $perspective) {
        $this->type = $type;
        $this->marketData = $data;
        $this->perspective = $perspective;
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@ibar-market-negotiation-market';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());

        // ensure defaulting is working
        $browser->assertValue('@market-negotiation-bid-qty', 500)
                ->assertValue('@market-negotiation-offer-qty', 500);
        
        // vue data is correct
        $browser->assertVue('marketNegotiation.bid_qty', 500, '@ibar-market-negotiation-market')
                ->assertVue('marketNegotiation.offer_qty', 500, '@ibar-market-negotiation-market');

    }

    public function hasLastNegotiationValues($browser)
    {
        $browser->within($this, function($browser) {
            // ensure defaulting is working
            $browser->assertValue('@market-negotiation-bid-qty', $this->marketData['user_market_negotiation']->bid_qty)
                    ->assertValue('@market-negotiation-bid', $this->marketData['user_market_negotiation']->bid)
                    ->assertValue('@market-negotiation-offer', $this->marketData['user_market_negotiation']->offer)
                    ->assertValue('@market-negotiation-offer-qty', $this->marketData['user_market_negotiation']->offer_qty);
            
            // vue data is correct
            $browser->assertVue('marketNegotiation.bid_qty', $this->marketData['user_market_negotiation']->bid_qty, '@ibar-market-negotiation-market')
                    ->assertVue('marketNegotiation.bid', $this->marketData['user_market_negotiation']->bid, '@ibar-market-negotiation-market')
                    ->assertVue('marketNegotiation.offer', $this->marketData['user_market_negotiation']->offer, '@ibar-market-negotiation-market')
                    ->assertVue('marketNegotiation.offer_qty', $this->marketData['user_market_negotiation']->offer_qty, '@ibar-market-negotiation-market');
        });
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '@ibar-market-negotiation-market',
        ];
    }
}
