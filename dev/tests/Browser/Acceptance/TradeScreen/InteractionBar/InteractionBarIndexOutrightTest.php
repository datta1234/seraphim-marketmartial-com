<?php

namespace Tests\Browser\Acceptance\TradeScreen\InteractionBar;

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

class InteractionBarIndexOutrightTest extends DuskTestCase
{
    use DatabaseMigrations, SetsUpUserMarketRequest;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();
        FactoryHelper::setUpTradeConditions();

        $this->marketData = $this->createaMarketData('TOP40', 'Outright');
        
    }

    public function testBoth() {
        $this->marketMaker();
        $this->marketInterest();
    }

    /**
     * A Dusk test example.
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
                // wait for the correct Tab + Contnet
                ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                ->waitForText($this->marketData['user_market_request_formatted']['trade_items']['default']['Strike'])
                ->within(new MarketTab($this->marketData['user_market_request_formatted']['id']), function($browser) {
                    
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->marketData['user_market_request_formatted']['id']));
                })
                ->waitFor(new InteractionBar)
                ->pause(500)
                ->screenshot($this->perspective.'_open')
                ->within(new InteractionBar,function($browser) {

                    // Title details displayed correctly
                    (new TitleBar('Outright',$this->marketData))->assert($browser);

                    // Market Hostory
                    (new MarketHistory('Outright',$this->marketData,$this->perspective))->assert($browser);

                    // Market Negotiations
                    (new MarketNegotiation('Outright',$this->marketData,$this->perspective))->assert($browser);

                    // submission buttons are present
                    $browser->assertVisible('@ibar-action-send')->assertVisible('@ibar-action-nocares');

                    // conditons
                    (new Conditions())->assert($browser);

                });
        });
    }

    /**
     * A Dusk test example.
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
                // wait for the correct Tab + Contnet
                ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                ->waitForText($this->marketData['user_market_request_formatted']['trade_items']['default']['Strike'])
                ->within(new MarketTab($this->marketData['user_market_request_formatted']['id']), function($browser) {
                    
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->marketData['user_market_request_formatted']['id']));
                })
                ->waitFor(new InteractionBar)
                ->pause(500)
                ->screenshot($this->perspective.'_open')
                ->within(new InteractionBar,function($browser) {

                    // Title details displayed correctly
                    (new TitleBar('Outright',$this->marketData))->assert($browser);

                    // Market Hostory
                    (new MarketHistory('Outright',$this->marketData,$this->perspective))->assert($browser);

                    // Market Negotiations
                    (new MarketNegotiation('Outright',$this->marketData,$this->perspective))->assert($browser);

                    // submission buttons are present
                    $browser->assertVisible('@ibar-action-send')->assertVisible('@ibar-action-nocares');

                    // conditons
                    (new Conditions())->assert($browser);

                });
        });
    }
}
