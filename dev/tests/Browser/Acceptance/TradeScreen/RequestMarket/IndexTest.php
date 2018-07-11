<?php

namespace Tests\Browser\TradeScreen\RequestMarket;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\TradeScreen;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket\StructureSelectionComponent;
use Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket\ExpirySelectionComponent;
use Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket\MarketSelectionComponent;
use Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket\StepSelectionComponent;
use Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket\DetailsComponent;
use Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket\ConfirmMarketRequestComponent;
use Tests\Helper\FactoryHelper;
use Carbon\Carbon;

class RequestMarketIndexTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    protected $organisation;
    protected $user;
    protected $markets;
    protected $tradeStructures;
    private $indexMarkets = [];      

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();
        $this->organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $this->user = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$this->organisation->id
        ]);
        $this->markets = \App\Models\StructureItems\Market::whereHas('marketType',function($query){
            $query->where('title','=','Index Option');
        })->get();

        $this->tradeStructures = \App\Models\StructureItems\TradeStructure::whereHas('marketTypes',function($query){
            $query->where('title','=','Index Option')
                  ->where('is_selectable','=',true);
        })->get();

    }


    /**
     * A Dusk test example.
     * @test
     * @return void
     */
    public function CreateIndexOutright()
    {
      
        $this->browse(function (Browser $browser) {
            
            $browser->loginAs($this->user)
                     ->visit(new TradeScreen)
                     ->within("@request-market-menu",function($browser){
                        $browser->waitUntilMissing('button[type="button"][disabled].mm-request-button')
                                ->click('button[type="button"].mm-request-button')
                                ->whenAvailable('.modal', function($modal) use ($browser){

                                $browser->whenAvailable('@step-selection', function($modal) use ($browser){
                                    $browser->within(new StepSelectionComponent, function ($browser) {
                                                $browser->assertSee('Index Options');
                                                $browser->assertSee('EFP');
                                                $browser->assertSee('Single Stock Options');
                                                $browser->assertSee('Rolls');
                                                $browser->assertSee('Options Switch');
                                                $browser->assertSee('EFP Switch');
                                                $browser->press('Index Options');
                                    });
                                });

                               $browser->whenAvailable('@market-selection', function($modal) use ($browser){

                                    $browser->within(new MarketSelectionComponent, function ($browser) {
                                        $browser->waitFor('.row .col-12',10);//need a more definitive selelctor based of loader                                        
                                        foreach ($this->markets as $market) 
                                        {
                                            $browser->assertSee($market->title);
                                        }
                                        //select a random market
                                        $browser->press($this->markets->random()->title);


                                    });

                                });

                                $browser->whenAvailable('@structure-selection', function($modal) use ($browser){
                                    $browser->within(new StructureSelectionComponent, function ($browser) {
                                        $browser->waitFor('.row .col-12',10);//need a more definitive selelctor based of loader                                        
                                        foreach ($this->tradeStructures as $tradeStructure) 
                                        {
                                              $browser->assertSee($tradeStructure->title);
                                        }

                                        $browser->press("Outright");

                                    });
                                });

                               $browser->whenAvailable('@expiry-selection', function($modal) use ($browser){
                                    $browser->within(new ExpirySelectionComponent, function ($browser) {
                                        // need a way to determin whick page a date will be on
                                        $browser->selectDate(\App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first());
                                        
                                    });
                                });




                                 
                                    // ->within(new StructureSelectionComponent, function ($browser) use ($user) {
                                    //         $browser->selectDate($user->birthdate);
                                    //     })
                                    // ->within(new ExpirySelectionComponent, function ($browser) use ($user) {
                                    //         $browser->selectDate($user->birthdate);
                                    //     })
                                    // ->within(new DetailsComponent, function ($browser) use ($user) {
                                    //         $browser->selectDate($user->birthdate);
                                    //     })
                                    // ->within(new ConfirmMarketRequestComponent, function ($browser) use ($user) {
                                    //         $browser->selectDate($user->birthdate);
                            });
                     });
                    

                });
    }

    private function getIndexMarket()
    {
        return null;
    }
}
