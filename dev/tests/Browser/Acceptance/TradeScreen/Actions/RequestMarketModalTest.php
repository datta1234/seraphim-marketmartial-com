<?php

namespace Tests\Browser\TradeScreen\Actions;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RequestMarketModalTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testSelectAMarket()
    {
        $this->browse(function(Browser $browser) {
            $browser->visit('/trade')
            ->click('#action-bar button[mm-request-market]')
            ->whenAvailable('.modal', function($modal) {

                $modal->within('.modal-header', function($modalHeader) {
                    $modalHeader->assertSee('Select Market');
                });

                $modal->within('.modal-body', function($modalBody) {
                    $modalBody->assertSee('Index Options');
                    $modalBody->assertSee('EFP');
                    $modalBody->assertSee('Single Stock Options');
                    $modalBody->assertSee('Rolls');
                    $modalBody->assertSee('Options Switch');
                    $modalBody->assertSee('EFP Switch');
                });

                $modal->within('.modal-footer', function($modalFooter) {
                    $modalFooter->assertSee('Cancel');
                });

            });
        });
    }

    public function testIndexOptions()
    {
        $this->browse(function(Browser $browser) {
            $browser->visit('/trade')
            ->click('#action-bar button[mm-request-market]')
            ->waitFor('.modal')
            ->click('.modal .modal-body button[mm-index-options]');
        });
    }
}
