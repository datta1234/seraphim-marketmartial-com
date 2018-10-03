<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\TradeScreen;

class ChangeThemeTest extends DuskTestCase
{

    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
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
    public function testChangeTheme()
    {
         $this->browse(function (Browser $browser) {
                $browser->loginAs($this->user)
                         ->visit(new TradeScreen)
                         ->assertVisible(".light-theme")
                         ->press("#theme-toggle")
                         ->assertVisible(".dark-theme");

             });
    }
}
