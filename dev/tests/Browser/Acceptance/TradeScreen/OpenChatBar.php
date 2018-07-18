<?php

namespace Tests\Browser\Acceptance\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Components\TradeScreen\ChatBar;
use Tests\Browser\Pages\TradeScreen;

class OpenChatBar extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $this->user = factory(\App\Models\UserManagement\User::class)->create([
            'organisation_id'=>$this->organisation->id
        ]);   
    }

    public function testViewChats()
    {
        $this->browse(function (Browser $browser) {

                $browser->loginAs($this->user)
                         ->visit(new TradeScreen)
                         ->press("#action-bar-open-chat")
                         ->pause(500)
                         ->within(new ChatBar,function($browser){
                            
                            $browser->assertSee("Messages");

                         });
             });
    }
}
