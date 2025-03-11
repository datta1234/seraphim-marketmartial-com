<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\TradeScreen\FilterMarket;
use Tests\Helper\FactoryHelper;

class FilterMarketTest extends DuskTestCase
{

    use DatabaseMigrations;
    private $marketText;
    private $marketTypes;
    private $markets;
    private $organisation;
    private $user;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        $this->marketText = collect(['INDEX'=>['TOP40','CTOP','CTOR'],
                                    'SINGLES'=>['SINGLES'],
                                    'DELTA ONE'=>['DELTA ONE']
                                ]);
        $this->marketTypes = \App\Models\StructureItems\MarketType::all()->keyBy('title');
        $this->markets = \App\Models\StructureItems\Market::all();
        $this->organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $this->user = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$this->organisation->id
        ]);   
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testShowAllMarkets()
    {
        $this->browse(function (Browser $browser) {
                $browser->loginAs($this->user)
                         ->visit(new TradeScreen)
                         ->within(new FilterMarket,function($browser){
                            $browser->open();
                         })
                        ->waitFor('.popover')
                        ->within('.popover',function($popover){
                            foreach ($this->marketText as $market => $values) 
                            {
                                $popover->assertSee($market)
                                        ->press("[data-add-market='{$market}']");
                            } 
                        })
                        ->within('.user-markets',function($browser){
                                foreach ($this->marketText  as $types) 
                                {
                                    foreach ($types as $type) 
                                    {
                                         $browser->waitForText($type);
                                    }
                                }
                        });

             });
    }


    public function testHideAllMarkets()
    {
        $this->user->marketInterests()->sync($this->markets->pluck('id'));

         $this->browse(function (Browser $browser) {
                $browser->loginAs($this->user)
                         ->visit(new TradeScreen)
                         ->within(new FilterMarket,function($browser){
                            $browser->open();
                         })
                        ->waitFor('.popover')
                        ->within('.popover',function($popover){
                            foreach ($this->marketText as $market => $values) 
                            {
                                $popover->assertSee($market)
                                        ->press("[data-remove-market='{$market}']");
                            } 
                        })
                        ->within('.user-markets',function($browser){
                                foreach ($this->marketText  as $types) 
                                {
                                    foreach ($types as $type) 
                                    {
                                         $browser->assertDontSee($type);
                                    }
                                }
                        });

             });
    }
}
