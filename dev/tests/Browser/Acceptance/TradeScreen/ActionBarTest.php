<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\TradeScreen\UserHeader;
use Tests\Helper\FactoryHelper;
use Tests\Helpers\Traits\SetsUpUserMarketRequest;
use Tests\Browser\Components\TradeScreen\InteractionBar;
use Tests\Browser\Components\TradeScreen\MarketTab;

use Carbon\Carbon;

class ActionBarTest extends DuskTestCase
{

    use DatabaseMigrations, SetsUpUserMarketRequest;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();
        $this->marketData = $this->createaMarketData('TOP40', 'Outright');
    }


    public function testRequestButton()
    {
       $this->browse(function (Browser $browser) {
                $browser->loginAs($this->marketData['user_interest'])
                         ->visit(new TradeScreen)
                         ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                         ->within(".menu-actions",function($browser){
                                $browser->assertSee("Request");
                         });
        });
    }


    public function testImportantButton()
    {
        $this->browse(function (Browser $browser) {
                $browser->loginAs($this->marketData['user_interest'])
                         ->visit(new TradeScreen)
                         ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                         ->within(".menu-actions",function($browser){
                                $browser->waitForText("Important 1");
                         });
        });
    }

    public function testAlertButton()
    {
        $this->browse(function (Browser $browser) {
                $browser->loginAs($this->marketData['user_interest'])
                         ->visit(new TradeScreen)
                         ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                         ->within(".menu-actions",function($browser){
                                $browser->waitForText("Alerts 1");
                         });
        });
    }

    public function testAlertsButton()
    {
            factory(App\Models\Market\MarketNegotiation::class)->create([
                "user_id" => $this->marketData['user_maker']->user->id,
                "user_market_id" => $this->marketData['user_makert']->id 
            ]); 

            $this->browse(function (Browser $browser) {
                $browser->loginAs($this->marketData['user_maker'])
                         ->visit(new TradeScreen)
                         ->waitFor(new MarketTab($this->marketData['user_market_request_formatted']['id']))
                         ->within(".menu-actions",function($browser){
                                $browser->waitForText("Alerts 1");
                         });
            });
    }

  });
    // }

}
