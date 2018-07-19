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
    private $selectedMarket;
    private $selectedTradeStructure;     
    private $selectedExpiryDates;
    private $items;

    protected function setUp()
    {
        parent::setUp();
        FactoryHelper::setUpMarkets();
        FactoryHelper::setUpTradeStructures();
        FactoryHelper::setUpExperationDates();

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
        })->with('tradeStructureGroups.items')->get();

        $this->selectedMarket;
      

    }


   
    private function assertstructure($selectedMarket,$selectedTradeStructure,$selectedExpiryDates,$items)
    {
        $this->selectedMarket = $selectedMarket;
        $this->selectedTradeStructure = $selectedTradeStructure;
        $this->selectedExpiryDates = $selectedExpiryDates;
        $this->items = $items;

        // dd( $this->selectedTradeStructure, $this->selectedExpiryDates,$this->items);

        $this->browse(function (Browser $browser) {

            //market
            //tradestructure
            //expiry date
            //tradesctructure items

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
                                        // $this->selectedMarket = $this->markets->random()->title;

                                        $browser->press($this->selectedMarket);
                                    });
                                });

                                $browser->whenAvailable('@structure-selection', function($modal) use ($browser){
                                    $browser->within(new StructureSelectionComponent, function ($browser) {
                                        $browser->waitFor('.row .col-12',10);//need a more definitive selelctor based of loader     


                                        foreach ($this->tradeStructures as $tradeStructure) 
                                        {
                                              $browser->assertSee($tradeStructure->title);
                                        }
                                        $browser->press($this->selectedTradeStructure);

                                    });
                                });

                               $browser->whenAvailable('@expiry-selection', function($modal) use ($browser){
                                    $browser->within(new ExpirySelectionComponent, function ($browser) {
                                        $browser->waitFor('.row .pagination',10);//need a more definitive selelctor based of loader     
                                         // $this->selectedExpiryDates = \App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first()->expiration_date;
                                        
                                        $browser->selectDate($this->selectedExpiryDates);
                                        
                                    });
                                });

                                $browser->whenAvailable('@detail', function($modal) use ($browser){
                                    $browser->within(new DetailsComponent, function ($browser) {
                                        
                                        //get the trade structure and handle str
                                        foreach ($this->items as $key => $item) 
                                        {
                                            $browser->type('#strike-'.$key,$item['strike']);
                                            $browser->type('#quantity-'.$key,$item['quantity']);
                                        }

                                        $browser->press("Submit");
                                    });
                                });


                                  $browser->whenAvailable('@confirm-market-request', function($modal) use ($browser){
                                    $browser->within(new ConfirmMarketRequestComponent, function ($browser) {
                                        $browser->assertDetails($this->selectedMarket,$this->selectedTradeStructure,$this->selectedExpiryDates,$this->items);
                                        $browser->press("Send Request");
                                        $browser->waitUntilMissing('.modal');


                                    });
                                });

                            });
                     });
                    

                });
    }

     /**
     * Test for outright index.
     * @test
     * @return void
     */
    public function testCreateIndexOutright()
    {
        $selectedMarket = $this->markets->random()->title;
        $selectedTradeStructure = "Outright";
        $selectedExpiryDates = [\App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first()->expiration_date];
        $items = [
            [
                'strike'    => 500,
                'quantity'  => 500
            ]
        ];
    
        $this->assertstructure($selectedMarket,$selectedTradeStructure,$selectedExpiryDates,$items);
    }

    /**
     * Test for Risky index.
     * @test
     * @return void
     */
    public function testCreateIndexRisky()
    {
        $selectedMarket = $this->markets->random()->title;
        $selectedTradeStructure = "Risky";
        $selectedExpiryDates = [\App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first()->expiration_date];
        $items = [
            [
                'strike'    => 500,
                'quantity'  => 500
            ],
            [
                'strike'    => 500,
                'quantity'  => 500
            ]
        ];
        $this->assertstructure($selectedMarket,$selectedTradeStructure,$selectedExpiryDates,$items);
    }

    /**
     * Test for Calander index.
     * @test
     * @return void
     */
    public function testCreateIndexCalander()
    {
        $selectedMarket = $this->markets->random()->title;
        $selectedTradeStructure = "Calendar";
        $selectedExpiryDates = [
            \App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first()->expiration_date,
            \App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first()->expiration_date,
        ];

        $items = [
            [
                'strike'    => 500,
                'quantity'  => 500
            ],
            [
                'strike'    => 500,
                'quantity'  => 500
            ],
        ];
    
        $this->assertstructure($selectedMarket,$selectedTradeStructure,$selectedExpiryDates,$items);
    }

    /**
     * Test for Fly index.
     * @test
     * @return void
     */
    public function testCreateIndexFly()
    {
        $selectedMarket = $this->markets->random()->title;
        $selectedTradeStructure = "Fly";
        $selectedExpiryDates = [\App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->first()->expiration_date];
        $items = [
            [
                'strike'    => 500,
                'quantity'  => 500
            ],
            [
                'strike'    => 600,
                'quantity'  => 600
            ],
            [
                'strike'    => 800,
                'quantity'  => 800
            ]
        ];
        $this->assertstructure($selectedMarket,$selectedTradeStructure,$selectedExpiryDates,$items);
    }

    private function getIndexMarket()
    {
        return null;
    }
}
