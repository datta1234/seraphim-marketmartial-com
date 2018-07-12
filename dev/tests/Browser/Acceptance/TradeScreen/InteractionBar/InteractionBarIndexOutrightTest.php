<?php

namespace Tests\Browser\Acceptance\TradeScreen\InteractionBar;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Helper\FactoryHelper;
use Tests\Helpers\Traits\SetsUpUserMarketReqeust;

use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\TradeScreen\InteractionBar;
use Tests\Browser\Components\TradeScreen\MarketTab;
use Tests\Browser\Components\TradeScreen\MarketTabs\MarketTabOutright;

class InteractionBarIndexOutrightTest extends DuskTestCase
{
    use DatabaseMigrations, SetsUpUserMarketReqeust;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();

        
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testMarketMaker()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs($this->user_maker)
                ->visit(new TradeScreen)
                // wait for the correct Tab + Contnet
                ->waitFor(new MarketTab($this->user_market_request_formatted['id']))
                ->waitForText($this->user_market_request_formatted['trade_items']['default']['Strike'])->screenshot(2)
                ->within(new MarketTab($this->user_market_request_formatted['id']), function($browser) {
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->user_market_request_formatted['id']))->screenshot(3);
                })
                ->waitFor(new InteractionBar)->screenshot(4)
                ->within(new InteractionBar,function($browser) {
                    $browser->assertSee($this->user_market_request->updated_at->format("H:i"))
                    ->assertSee('Apply a condition');

                });
        });
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testInterest()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 1080)
                ->loginAs($this->user)
                ->visit(new TradeScreen)
                // wait for the correct Tab + Contnet
                ->waitFor(new MarketTab($this->user_market_request_formatted['id']))
                ->waitForText($this->user_market_request_formatted['trade_items']['default']['Strike'])->screenshot(2)
                ->within(new MarketTab($this->user_market_request_formatted['id']), function($browser) {
                    // Click on the qualifiying record
                    $browser->click(new MarketTab($this->user_market_request_formatted['id']))->screenshot(3);
                })
                ->waitFor(new InteractionBar)->screenshot(4)
                ->within(new InteractionBar,function($browser) {
                    $browser->assertSee($this->user_market_request->updated_at->format("H:i"))
                    ->assertSee('Apply a condition');

                });
        });
    }
}
