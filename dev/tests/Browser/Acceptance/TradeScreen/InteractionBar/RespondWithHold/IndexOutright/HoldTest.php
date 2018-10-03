<?php

namespace Tests\Browser\Acceptance\TradeScreen\InteractionBar\RespondWithHold\IndexOutright;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Helper\FactoryHelper;
use Tests\Helpers\Traits\SetsUpUserMarketRequest;

use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\TradeScreen\InteractionBar;
use Tests\Browser\Components\TradeScreen\InteractionBar\TitleBar;
use Tests\Browser\Components\TradeScreen\InteractionBar\MarketHistory;
use Tests\Browser\Components\TradeScreen\InteractionBar\MarketNegotiation;
use Tests\Browser\Components\TradeScreen\InteractionBar\Conditions;
use Tests\Browser\Components\TradeScreen\MarketTab;
use Tests\Browser\Components\TradeScreen\MarketTabs\MarketTabOutright;

class HoldTest extends DuskTestCase
{
    use DatabaseMigrations, SetsUpUserMarketRequest;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();
        FactoryHelper::setUpTradeConditions();

        $marketNegotiation = [
            'bid'       => 28,
            'offer'     => 29,
            'bid_qty'   => 500,
            'offer_qty' => 500,
        ];

        $this->marketData = $this->createaMarketData('TOP40', 'Outright', [], [], $marketNegotiation);
    }

    public function testBothVolspread() {
        $this->marketMaker();
        $this->marketInterest();
    }

    public function testBothBidOnly() {
        $this->marketData['user_market_negotiation']->update(['offer' => null]);
        $this->marketData['reset_formatted']();
        $this->marketMaker();
        $this->marketInterest();
    }

    public function testBothOfferOnly() {
        $this->marketData['user_market_negotiation']->update(['bid' => null]);
        $this->marketData['reset_formatted']();
        $this->marketMaker();
        $this->marketInterest();
    }

    /**
     * A market Maker perspective test.
     *
     * @todo Test alert button popup and can also open interaction bar
     * @todo Add 3de party tests
     *
     * @return void
     */
    public function marketMaker()
    {   
        $this->perspective = 'maker';
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs($this->marketData['user_'.$this->perspective])
                ->visit(new TradeScreen)
                //wait for the alert to show and check count
                ->waitFor(new AlertsButton)
                ->within(new AlertsButton, function($browser){
                    $browser->assertCount(1);
                })
                // wait for the correct Tab + Content
                ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                ->waitForText($this->marketData['user_market_request_formatted']['trade_items']['default']['Strike'])
                ->within(new MarketTab($this->marketData['user_market_request_formatted']['id']), function($browser) {
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                        ->assertVisible('.user-action');
                })
                ->waitFor(new InteractionBar)
                ->pause(500)
                ->screenshot($this->perspective.'_open')
                ->within(new InteractionBar,function($browser) {

                    // Title details displayed correctly
                    (new TitleBar('Outright',$this->marketData))->assert($browser);

                    // Market History
                    $browser->within(new MarketHistory('Outright',$this->marketData,$this->perspective), function($browser) {
                        $browser->assertVisible('.user-action')
                            ->testHoldText()
                            ->makerAssertVol();
                    });

                    // Market Negotiations
                    $browser->within(new MarketNegotiation('Outright',$this->marketData,$this->perspective), function($browser){
                        $browser->assertVol()
                            ->assertQty();
                    });

                    // hold buttons are present and no cares and repeat is missing
                    $browser->assertMissing('@send-button')
                        ->assertMissing('@nocares-button')
                        ->assertVisible('@repeat-button')
                        ->assertVisible('@pull-button')
                        ->assertVisible('@amend-disabled-button');

                    // check button changes states when bid input value changes
                    if($this->marketData['user_market_negotiation']->bid != null) {
                        // check bid input value set to quote bid value
                        $browser->assertValue('@market-negotiation-bid', $this->marketData['user_market_negotiation']->bid)
                            // amend quote bid input value
                            ->type('@market-negotiation-bid', $this->marketData['user_market_negotiation']->bid + 1)
                            // quote amended button should be enabled
                            ->waitUntilMissing('@amend-disabled-button')
                            // reset quote bid input values back    
                            ->type('@market-negotiation-bid', $this->marketData['user_market_negotiation']->bid - 1)
                            // quote reverted button should be disabled again
                            ->waitFor('@amend-disabled-button');
                    } else {
                        $browser->assertValue('@market-negotiation-bid', '');
                    }
                    // check button changes states when offer input value changes
                    if($this->marketData['user_market_negotiation']->offer != null) {
                        // check offer input value set to quote offer value
                        $browser->assertValue('@market-negotiation-offer', $this->marketData['user_market_negotiation']->offer)
                            // amend quote offer input value
                            ->type('@market-negotiation-offer', $this->marketData['user_market_negotiation']->offer + 1)
                            // quote amended button should be enabled
                            ->waitUntilMissing('@amend-disabled-button')
                            // reset quote offer input value back
                            ->type('@market-negotiation-offer', $this->marketData['user_market_negotiation']->offer - 1)
                            // quote reverted button should be disabled again
                            ->waitFor('@amend-disabled-button');
                    } else {
                        $browser->assertValue('@market-negotiation-offer', '');
                    }                 

                });
        });
    }

    /**
     * A market Intrest perspective test.
     * 
     * @todo Test alert button popup and can also open interaction bar
     * @todo Add 3de party tests
     *
     * @return void
     */
    public function marketInterest()
    {

        $this->perspective = 'interest';

        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs($this->marketData['user_'.$this->perspective])
                ->visit(new TradeScreen)
                //wait for the alert to show and check count
                ->waitFor(new AlertsButton)
                ->within(new AlertsButton, function($browser){
                    $browser->assertCount(1);
                })
                // wait for the correct Tab + Content
                ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                ->waitForText($this->marketData['user_market_request_formatted']['trade_items']['default']['Strike'])
                ->within(new MarketTab($this->marketData['user_market_request_formatted']['id']), function($browser) {
                    
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                        ->assertSee('RECEIVED');
                })
                ->waitFor(new InteractionBar)
                ->pause(500)
                ->screenshot($this->perspective.'_open')
                ->within(new InteractionBar,function($browser) {

                    // Title details displayed correctly
                    (new TitleBar('Outright',$this->marketData))->assert($browser);

                    // Market History
                    $browser->within(new MarketHistory('Outright',$this->marketData,$this->perspective), function($browser) {
                        $browser->assertVisible('.user-action')
                            ->testHoldText()
                            ->interestAssertVol()
                            ->timestampAssert()
                            ->assertVisible('@hold-button')
                            ->assertVisible('@accept-button');
                    });

                    // Market Negotiations
                    (new MarketNegotiation('Outright',$this->marketData,$this->perspective))->assert($browser);

                    // submission buttons are present
                    $browser->assertVisible('@nocares-button')
                        ->assertMissing('@send-disabled-button')
                        // quote bid input value
                        ->type('@market-negotiation-bid', 25)
                        // send button should be enabled
                        ->waitUntilMissing('@send-disabled-button')
                        // remove bid input values    
                        ->type('@market-negotiation-bid', '')
                        // send button should be disabled again
                        ->waitFor('@send-disabled-button')
                        // quote offer input value
                        ->type('@market-negotiation-offer', 25)
                        // send button should be enabled
                        ->waitUntilMissing('@send-disabled-button')
                        // remove offer input values    
                        ->type('@market-negotiation-offer', '')
                        // send button should be disabled again
                        ->waitFor('@send-disabled-button');

                    // Market History set quote on hold
                    $browser->within(new MarketHistory('Outright',$this->marketData,$this->perspective), function($browser) {
                        $browser->click('@hold-button')
                        ->waitFor('@selected-hold-button');
                    });

                    $browser->click('@int-bar-close');
                })
                ->pause(500)
                ->within(new MarketTab($this->marketData['user_market_request_formatted']['id']), function($browser) {
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->marketData['user_market_request_formatted']['id']));
                })
                ->waitFor(new InteractionBar)
                ->pause(500)
                ->screenshot($this->perspective.'_reopen')
                ->within(new InteractionBar,function($browser) {
                    // Market History check quote still on hold
                    $browser->within(new MarketHistory('Outright',$this->marketData,$this->perspective), function($browser) {
                        $browser->waitFor('@selected-hold-button');
                    });

                    $browser->click('@int-bar-close');
                });
        });
    }
}
