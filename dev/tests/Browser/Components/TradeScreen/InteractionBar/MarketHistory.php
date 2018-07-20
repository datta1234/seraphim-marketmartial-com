<?php

namespace Tests\Browser\Components\TradeScreen\InteractionBar;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class MarketHistory extends BaseComponent
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
        return '@ibar-negotiation-history-market';
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

        switch($this->type) {
            // Outright
            case 'Outright':
                $this->testOutright($browser);
            break;
        }

        if($this->perspective == 'interest') {
            $browser->assertSee("Note: All quotes will default to HOLD after 30 minutes from the receipt of response has lapsed.");
        } else {
            $browser->assertDontSee("Note: All quotes will default to HOLD after 30 minutes from the receipt of response has lapsed.");
        }
    }

    public function testHoldText(Browser $browser)
    {
        if($this->perspective == 'maker') {
            $browser->assertSee("Interest has put your market on hold. Would you like to improve your spread?");
        } else {
            $browser->assertDontSee("Interest has put your market on hold. Would you like to improve your spread?");
        }
    }

    public function testOutright(Browser $browser)
    {
        if($this->marketData['user_market_request_formatted']['quotes'][0]['bid_only']) {
            $browser->assertSee('BID ONLY');
        }   
        elseif($this->marketData['user_market_request_formatted']['quotes'][0]['offer_only']) {
            $browser->assertSee('OFFER ONLY');
        } else {
            $browser->assertSee($this->marketData['user_market_request_formatted']['quotes'][0]['vol_spread'].' VOL SPREAD');
        }
    }

    public function clickHold($browser) {
        return $browser->within($this, function($browser) {
            $browser->click('@market-hold-btn-'.$this->marketData['user_market']->id);

        });
    }

    public function hasOnHoldMessage($browser) {
        return $browser->within($this, function($browser) {
            $browser->assertSee("Interest has put your market on hold. Would you like to improve your spread?")
        })
    }

    public function makerAssertVol(Browser $browser)
    {
          switch($this->type) {
                // Outright
                case 'Outright':
                    if($bid != null && ) {
                        $browser->assertSee($this->marketData['user_market_negotiation']->bid);
                    }
                    if($offer != null) {
                        $browser->assertSee($this->marketData['user_market_negotiation']->offer);
                    }
                    $browser->assertSee($this->marketData['user_market_negotiation']->bid_qty);
                    $browser->assertSee($this->marketData['user_market_negotiation']->offer_qty);
                    break;
            }
    }

    public function interestAssertVol(Browser $browser)
    {
            switch($this->type) {
                case 'Outright':
                    if($this->marketData['user_market_negotiation']->offer == 0)
                    {
                        $browser->assertSee('BID ONLY');
                    }else if($this->marketData['user_market_negotiation']->bid == 0)
                    {
                        $browser->assertSee('OFFER ONLY');
                    }else
                    {
                      $browser->assertSee($this->marketData['user_market_negotiation']->offer - $this->marketData['user_market_negotiation']->bid.' VOL SPREAD');
                    }
                break;

            }
    }

    public function timestampAssert(Browser $browser)
    {
        $browser->assertSee($this->marketData['user_market_negotiation']->updated_at->format('H:i'));
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '@ibar-negotiation-history-market',
            '@hold-button' => 'button[type="button"].hold-quote',
            '@selected-hold-button' => 'button[type="button"].hold-quote.active',
            '@accept-button' => 'button[type="button"].accept-quote'
            '@selected-accept-button' => 'button[type="button"].accept-quote.active'
        ];
    }
}
