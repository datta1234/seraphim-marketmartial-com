<?php

namespace Tests\Browser\Acceptance\Profile;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\InterestSettingsPage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Components\DayMonthPicker;

class InterestSettingsUpdate extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**
     * A Dusk test example.
     * @test
     * @return void
     */
    public function updateInterest()
    {
        
        $organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $user = factory(\App\Models\UserManagement\User::class)->create([
            'tc_accepted' => false,
            'organisation_id'=>$organisation->id
        ]);

         $this->browse(function (Browser $browser) use($user){
                $browser->loginAs($user)
                        ->visit(new InterestSettingsPage)
                        ->assertSee('Tell Us More About Yourself')
                        ->within(new DayMonthPicker, function ($browser) use ($user) {
                            $browser->selectDate($user->birthdate);
                        })
                        ->radio('@is_married', $user->is_married ? 1 : 0 )
                        ->radio('@has_children', $user->has_children ? 1 :0 ) 
                        ->type('@hobbies',$user->hobbies)
                        ->press('@submit')
                        ->waitForLocation('/terms-and-conditions')
                        ->assertSee('You\'re done. Thank you!');
            });
    }
}
