<?php

namespace Tests\Browser\Acceptance\Profile;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\ChangePasswordPage;
use Tests\Browser\Pages\EmailsettingsPage;

class ChangePassword extends DuskTestCase
{

    use DatabaseMigrations;

    /**
    * @test
    * @group FirstTimeLogin
    * @group UpdateChangePassword
    */
    public function FirstChangePassword()
    {
        $password = str_random(8);
        $newPassword = str_random(8);

        $organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $user = factory(\App\Models\UserManagement\User::class)->create([
            'tc_accepted' => false,
            'password' =>  bcrypt($password),
            'organisation_id' => $organisation->id
        ]);
        // make labels as they are use on email page
        $defaultLabels = factory(\App\Models\UserManagement\DefaultLabel::class,4)->create();

        $this->browse(function (Browser $browser) use($user, $password, $newPassword){

            $browser->loginAs($user)
                    ->visit(new ChangePasswordPage) //user should be redirect to my profile
                    ->assertSee('Change Password')
                    ->type('@oldPassword', $password)
                    ->type('@password',$newPassword)
                    ->type('@confirmPassword',$newPassword)
                    ->press('@submit')
                    ->waitForLocation((new EmailsettingsPage)->url())
                    ->screenshot('emailtest')
                    ->assertSee('E-Mail Settings');
        });
    }
}
