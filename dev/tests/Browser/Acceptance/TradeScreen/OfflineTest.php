<?php

namespace Tests\Browser\Acceptance\TradeScreen;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\TradeScreen;
use Carbon\Carbon;
use Config;


class OfflineTest extends DuskTestCase
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
     * Test to see trade operations are not allowed at the specified times.
     * @test
     * @return void
     */
    public function testOffline()
    {
        $startTime = Carbon::now()->subHours(2); 
        $endTime = Carbon::now()->subHours(2);

        Config::set('marketmartial.operation_window', [ 
                'start_time' => $startTime->format("H:i:s"),
                'end_time' => $endTime->format("H:i:s")
            ]
        );

        var_dump("here",config('marketmartial.operation_window.start_time'));

         $this->browse(function (Browser $browser) {
                $browser->loginAs($this->user)
                         ->visit(new TradeScreen)
                         ->resize(1920, 1080)
                         ->screenshot("now")
                         ->assertSee("Page Not Available between");

             });
    }
}
