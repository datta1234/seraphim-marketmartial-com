<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ActionBarTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testRequestMarketButton()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#action-bar button[mm-request-market]', function($browser) {
                    $browser->assertSee('Request A Market');
                });
        });
    }

    // public function testImportantButton()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->within('#action-bar button[mm-important]', function($browser) {
    //                 $browser->assertSee('Important');
    //             });
    //     });
    // }

    // public function testAlertsButton()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->within('#action-bar button[mm-alerts]', function($browser) {
    //                 $browser->assertSee('Alerts');
    //             });
    //     });
    // }

    // public function testConfirmationsButton()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->within('#action-bar button[mm-confirmations]', function($browser) {
    //                 $browser->assertSee('Confirmations');
    //             });
    //     });
    // }


    public function testAddMarketsButton()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#action-bar button[mm-add-market]', function($browser) {
                    $browser->assertSee('Markets');
                });
        });
    }

    public function testChatButton()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#action-bar button[mm-add-market]', function($browser) {
                    $browser->assertSee('Markets');
                });
        });
    }

}
