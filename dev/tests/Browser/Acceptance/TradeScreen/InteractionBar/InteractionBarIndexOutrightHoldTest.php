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

class InteractionBarIndexOutrightHoldTest extends DuskTestCase
{
    use DatabaseMigrations, SetsUpUserMarketRequest;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();
        FactoryHelper::setUpTradeConditions();

        $this->marketData = $this->createaMarketData('TOP40', 'Outright');
        dd($this->marketData);
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
        //To Display intbar - Open alerts dropdown and click alert.
        //To Display intbar - Click market tab
        //assert history - check for other org "BID ONLY" , "OFFER ONLY" and  "{num} VOL SPREAD"
        //assert history - check for my org BID ONLY - "Qty  bid    'nothing'   Qty" - "500    54          500"
        //assert history - check for my org OFFER ONLY - "Qty  'nothing'   Offer   Qty" - "500         54  500"
        //assert history - check for my org VOL SPREAD - "Qty  bid    'nothing'   Qty" - "500  52     54  500"
        //assert history - timestamps for all quotes format (HH:mm)

        //assert lvls marked in yellow
        //assert text displayed - "Interest has put your market on hold. Would you like to improve your spread?"

        //assert input boxes populated with last amount entered by $this org

        //assert display amend, repeat and pull buttons
        //assert amend disabled untill values entered into bid or offer


        $this->perspective = 'maker';

        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs($this->marketData['user_'.$this->perspective])
                ->visit(new TradeScreen)
                // wait for the correct Tab + Content
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

                    // Market History
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
        //To Display intbar - Open alerts dropdown and click alert.
        //To Display intbar - Click market tab
        // assert market tab displays “RECEIVED”

        //display intbar
        //assert title

        //assert history - check for other org "BID ONLY" , "OFFER ONLY" and  "{num} VOL SPREAD"
        //assert history - check for my org BID ONLY - "Qty  bid    'nothing'   Qty" - "500    54          500"
        //assert history - check for my org OFFER ONLY - "Qty  'nothing'   Offer   Qty" - "500         54  500"
        //assert history - check for my org VOL SPREAD - "Qty  bid    'nothing'   Qty" - "500  52     54  500"
        //assert history - timestamps for all quotes format (HH:mm)
        //assert history - hold and accept buttons

        //assert send button is disabled untill input boxes filled in
        //assert enter bid/offer - input boxes display - fill boxes
        //assert send button is enabled

        //user clicks hold
        //assert hold is selected
        //close int bar, open int bar
        //assert hold is still selected

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

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function marketMakerAlsoInterest()
    {
        $this->perspective = 'interest';

    }
}
