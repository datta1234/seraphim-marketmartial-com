<?php

namespace Tests\Browser\Acceptance\Profile;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\MyProfilePage;
use Tests\Browser\Pages\ChangePasswordPage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Profileupdate extends DuskTestCase
{
    use DatabaseMigrations;

    /**
    * @test
    * @group FirstTimeLogin
    * @group Updateprofile
    */
    public function FirstUpdateProfile()
    {
       $organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
       $user = factory(\App\Models\UserManagement\User::class)->create([
            'tc_accepted' => false,
            'organisation_id'=>$organisation->id
        ]);

       $updateUser = factory(\App\Models\UserManagement\User::class)->make([
            'tc_accepted' => false,
        ]); 

        $this->browse(function (Browser $browser) use($user, $updateUser, $organisation){
            $browser->loginAs($user)
                    ->visit(new MyProfilePage) //user should be redirect to my profile
                    ->assertSee('Your Profile')
                    ->type('@fullname',$updateUser->full_name)
                    ->type('@cellphone',$updateUser->cell_phone)
                    ->type('@workphone',$updateUser->work_phone)
                    ->type('@email',$updateUser->email)
                    ->select('@organisation',$organisation->id)
                    ->press('@submit')
                    ->waitForLocation((new ChangePasswordPage)->url())
                    ->assertSee('Change Password');
        });
    }
}
