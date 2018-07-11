<?php

namespace Tests\Browser\Acceptance\TradeScreen\InteractionBar;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Helper\FactoryHelper;

use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\TradeScreen\InteractionBar;
use Tests\Browser\Components\TradeScreen\MarketTab;

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

        // market request
        $this->user_market_request = factory(\App\Models\MarketRequest\UserMarketRequest::class)->create([
            'user_id'               =>  $this->user->id,
            'trade_structure_id'    =>  \App\Models\StructureItems\TradeStructure::where('title', 'Outright')->first()->id,
            'market_id'             =>  \App\Models\StructureItems\Market::where('title', 'TOP40')->first()->id,
        ]);
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
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testMarketMaker()
    {
        $this->browse(function (Browser $browser) {
             $browser->loginAs($this->user)
                ->visit(new TradeScreen)
                ->click(new MarketTab)
                ->waitFor(new InteractionBar)
                ->within(new InteractionBar,function($browser) {
                    $browser->assertSee('Apply a condition');
                });
        });
    }
}
