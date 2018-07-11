<?php

namespace Tests\Browser\Acceptance\Profile;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TermsOfUse extends DuskTestCase
{
    use DatabaseMigrations;
    /**
    * @test
    * @group FirstTimeLogin
    * @group AcceptTermsOfUse
    */
    public function FirstUpdateProfile()
    {

     

        $this->browse(function (Browser $browser) {
        
        $organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $user = factory(\App\Models\UserManagement\User::class)->create([
            'tc_accepted' => false,
            'organisation_id'=>$organisation->id
        ]);


        $browser->loginAs($user)
                ->visit('/terms-and-conditions')
                ->assertSee('You\'re done. Thank you!')
                ->check('tc_accepted')
                ->press("Next")
                ->waitForLocation('/terms-and-conditions')
                ->assertSee("Please wait for your account to be verified.");

        });
    }
}
