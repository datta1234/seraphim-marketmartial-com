<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\Organisation;
use Tests\Browser\Pages\LoginPage;
use Tests\Browser\Pages\TradeScreen;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testHtml()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)

                // html markup
                ->assertTitle('Market Martial');
        });
    }

    public function testLogin()
    {
        $this->browse(function ($browser) {
            $role = factory(Role::class)->create();
            $organisation = factory(Organisation::class)->create();
            $user = factory(User::class)->create([
                        'organisation_id' =>  $organisation->id,
                        'password'  =>  \Hash::make('samplepass')
                    ]);
            $browser->visit(new LoginPage)
                    ->type('#loginPageLoginForm input[name="email"]', $user->email)
                    ->type('#loginPageLoginForm input[name="password"]', 'samplepass')
                    ->press('#loginPageLoginForm button[type="submit"]')
                    ->waitForLocation((new TradeScreen)->url())
                    ->assertSeeLink('Logout');
        });
    }

    public function testContent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new LoginPage)              
                // see the main phrase
                ->assertSee('Login')

                // important sections
                ->assertSee('Forgot Your Password?')
                ->assertSee('Remember Me')

                // menu tests
                ->assertSeeLink('About Us')
                ->assertSeeLink('Contact Us')
                ->assertSeeLink('Sign up now');
        });
    }
}
