<?php

namespace Tests\Browser\Acceptance\TradeScreen\InteractionBar;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Helper\FactoryHelper;

use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\TradeScreen\InteractionBar;
use Tests\Browser\Components\TradeScreen\MarketTab;
use Tests\Browser\Components\TradeScreen\MarketTabs\MarketTabOutright;

class InteractionBarIndexOutrightTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();

        // interest
        $this->organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $this->user = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$this->organisation->id
        ]);
        // market maker
        $this->organisation_maker = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $this->user_maker = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$this->organisation_maker->id
        ]);
        
        $this->market = \App\Models\StructureItems\Market::where('title', 'TOP40')->first();
        $this->trade_structure = \App\Models\StructureItems\TradeStructure::where('title', 'Outright')->first();

        // market request
        $this->user_market_request = factory(\App\Models\MarketRequest\UserMarketRequest::class)->create([
            'user_id'               =>  $this->user->id,
            'trade_structure_id'    =>  $this->trade_structure->id,
            'market_id'             =>  $this->market->id,
        ]);
        $this->trade_structure->tradeStructureGroups->each(function($group) {
            $userMarketGroup = factory(\App\Models\MarketRequest\UserMarketRequestGroup::class)->create([
                'trade_structure_group_id'  =>  $this->trade_structure->id,
                'user_market_request_id'    =>  $this->user_market_request->id
            ]);
            $items = [];
            $group->items->each(function($item) use (&$items, $userMarketGroup) {
                $items[] = factory(\App\Models\MarketRequest\UserMarketRequestItem::class)->create([
                    'user_market_request_group_id'  =>  $userMarketGroup->id,
                    'item_id'    =>  $item->id
                ]);
            });
        });
        // user market
        $this->user_market = $this->user_market_request->userMarkets()->create([
            'user_id' => $this->user->id
        ]);
        // market negotiation
        $this->user_market_negotiation = $this->user_market->marketNegotiations()->create([
            'user_id'               =>  $this->user->id,
            'counter_user_id'       =>  $this->user->id,
            'market_negotiation_id' =>  null,

            'future_reference'      =>  0,
            'has_premium_calc'      =>  false,

            'is_private'            =>  true,
        ]);
        $this->user_market->currentMarketNegotiation()->associate($this->user_market_negotiation)->save();
        
        $this->user_market_request_formatted = $this->user_market_request->preFormatted();
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
                    echo 'clicked';
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
                    echo 'clicked';
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
