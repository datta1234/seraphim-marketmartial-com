<?php

namespace Tests\Browser\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FooterTest extends DuskTestCase
{
    public function testLinks()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trade')
                ->within('footer', function($browser) {
                    $browser->assertSeeLink('Fee Structure');
                    $browser->assertSeeLink('Conditions Explained');
                });
        });
    }

    // public function testCopywrite()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->visit('/trade')
    //             ->within('footer', function($browser) {
    //                 $browser->assertSee('');
    //             });
    //     });
    // }
}
