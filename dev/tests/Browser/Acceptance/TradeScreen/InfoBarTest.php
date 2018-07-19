<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\TradeScreen\UserHeader;
use Carbon\Carbon;

class InfoBarTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $this->user = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$this->organisation->id
        ]);   
        $marketTypes = \App\Models\StructureItems\MarketType::all();

    }

    public function testUserHeader()
    {
        $this->browse(function (Browser $browser) {

                $browser->loginAs($this->user)
                         ->visit(new TradeScreen)
                         ->within(new UserHeader,function($browser){
                           
                            $browser->checkTime(Carbon::now());
                            $browser->checkWelcomeMessage($this->user);
                            $browser->checkRebates($this->user);

                         });
             });
    }
   
}
