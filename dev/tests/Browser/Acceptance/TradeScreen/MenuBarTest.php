<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MenuBarTest extends DuskTestCase
{
    public function testTradeMenuItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#menu-bar', function($browser) {
                    $browser->assertSeeLink('Trade');
                });
        });
    }

    public function testStatsMenuItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#menu-bar', function($browser) {
                    $browser->assertSeeLink('Stats');
                });
        });
    }

    public function testPreviousDayMenuItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#menu-bar', function($browser) {
                    $browser->assertSeeLink('Previous Day');
                });
        });
    }

    public function testMoreMenuItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#menu-bar', function($browser) {
                    $browser->assertSeeLink('More')
                    ->clickLink('More')
                    ->seeLink('Account Settings')
                    ->seeLink('Rebates Summary');
                });
        });
    }

    public function testLogoutMenuItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#menu-bar', function($browser) {
                    $browser->assertSeeLink('Logout');
                });
        });
    }
}
