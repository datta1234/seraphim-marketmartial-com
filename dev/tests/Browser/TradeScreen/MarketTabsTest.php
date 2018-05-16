<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MarketTabsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testDefaultMarkets()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->assertSee('TOP40')
                ->assertSee('SINGLES');
        });
    }

    // public function testDTOP()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->assertSee('DTOP');
    //     });
    // }

    // public function testDCAP()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->assertSee('DCAP');
    //     });
    // }

    // public function testDELTA()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->assertSee('DELTA');
    //     });
    // }
}
