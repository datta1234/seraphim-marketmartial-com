<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class InfoBarTest extends DuskTestCase
{
    // @TODO: factory user login - test naem displayed
    // public function testWelcome Message()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->within('#info-bar', function($browser) {
    //                 // $browser->assertSee('Previous Day');
    //             });
    //     });
    // }

    public function testClock()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#info-bar', function($browser) {
                    $browser->assertSee('clock');
                });
        });
    }

    public function testRebatesDispaly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('#info-bar', function($browser) {
                    $browser->assertSee('Rebates');
                    // @TODO: value seeded by factory
                });
        });
    }
}
