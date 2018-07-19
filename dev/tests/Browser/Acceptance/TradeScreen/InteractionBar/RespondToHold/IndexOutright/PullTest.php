<?php

namespace Tests\Browser\Acceptance\TradeScreen\InteractionBar\RespondToHold;

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
use  Tests\Browser\Components\TradeScreen\ActionBar\AlertsButton;
use Tests\Browser\Components\TradeScreen\MarketTabs\MarketTabOutright;

class RepeatTest extends DuskTestCase
{
    use DatabaseMigrations, SetsUpUserMarketRequest;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();
        FactoryHelper::setUpTradeConditions();
        


        $this->marketData = $this->createaMarketData('TOP40', 'Outright',[],[
            'is_on_hold' => true
        ]);
        
        $this->userMarket = Factory(\App\Models\Market\UserMarket::class)->create([
            'user_market_request_id' => $this->marketData['user_market_request']->id,
            'user_id' => $this->marketData['user_maker']->id
        ]);

        
        // negotiation
        $this->marketNegotiation = $this->userMarket
            ->marketNegotiations()
            ->create([
                'bid' => 100,
                'offer' => 100,
                'bid_qty' => $this->userMarket["user_market_negotiation"]['bid_qty'],
                'offer_qty' => $this->userMarket["user_market_negotiation"]['offer_qty'],
                'is_repeat' => false,
                'has_premium_calc' => false,
                'bid_premium' => null,
                'offer_premium' => null,
                'user_id' => $this->marketData['user_maker']->id
        ]);


        $this->userMarket
            ->currentMarketNegotiation()
            ->associate($this->marketNegotiation)
            ->save();

    }

    public function testAmend() {
        $this->marketMakerAmend();
        $this->interestAmend();

    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function marketMakerRepeat()
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

                

                    $browser->press('Pull');
                    $browser->AssertDontSee('Repeat');
                    $browser->AssertSee('Amend');
                    $browser->AssertSee('Pull');

                    (new MarketNegotiation('Outright',$this->marketData,$this->perspective))->assertVol($browser,$this->marketNegotiation->bid,$this->marketNegotiation->offer);
                   


                });
        });
    }

    public function interestRepeat()
    {
        $this->perspective = 'interest';

        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs($this->marketData['user_'.$this->perspective])
                ->visit(new TradeScreen)
                // wait for the correct Tab + Contnet
                ->waitFor(new AlertsButton)
                ->within(new AlertsButton, function($browser){
                    $browser->assertCount(1);
                })
                ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                ->waitForText($this->marketData['user_market_request_formatted']['trade_items']['default']['Strike'])
                ->within(new MarketTab($this->marketData['user_market_request_formatted']['id']), function($browser) {
                    
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->marketData['user_market_request_formatted']['id']));
                     


                })->waitFor(new InteractionBar)
                ->pause(500)
                ->screenshot($this->perspective.'_open')
                ->within(new InteractionBar,function($browser){

                    (new MarketHistory('Outright',$this->marketData,$this->perspective))->interestAssertVol($browser,$this->marketNegotiation->bid,$this->marketNegotiation->offer);
                });
        });

    }


     /**
     * A Dusk test example.
     *
     * @return void
     */
    public function marketMakerAmend()
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

                    (new MarketNegotiation('Outright',$this->marketData,$this->perspective))->ammendVol($browser,$this->marketNegotiation->bid,$this->marketNegotiation->offer);

                    $browser->click('@ibar-action-send')
                            ->WaitForText('Response sent to Interest.');
                            
                            (new MarketNegotiation('Outright',$this->marketData,$this->perspective))->assert($browser);
                            
                            (new MarketHistory('Outright',$this->marketData,$this->perspective))
                            ->assertVol($browser,$this->marketNegotiation->bid,$this->marketNegotiation->offer);


                });
        });
    }

}
