<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\Organisation;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\TradeScreen;

class HomePageTest extends DuskTestCase
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
            $browser->visit(new HomePage)

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
            $browser->visit(new HomePage)
                    ->type('#homePageLoginForm input[name="email"]', $user->email)
                    ->type('#homePageLoginForm input[name="password"]', 'samplepass')
                    ->press('#homePageLoginForm button[type="submit"]')
                    ->waitForLocation((new TradeScreen)->url())
                    ->assertSeeLink('Logout');
        });
    }

    public function testContent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage)
                ->assertSee('Active Market Makers Online')
                
                // see the main phrase
                ->assertSee('The Inter-Bank Derivatives')
                ->assertSee('Trading Platform')

                // title sections
                ->assertSee('What does Market Martial do?')
                ->assertSee('We improve liquidity')
                ->assertSee('Electronic efficiency')
                ->assertSee('We maintain the feel and flow')
                ->assertSee('We bridge the gap: implied volatility vs premium')
                ->assertSee('You are our priority')

                // menu tests
                ->assertSeeLink('About Us')
                ->assertSeeLink('Contact Us')
                ->assertSeeLink('Sign up now');
        });
    }

    public function testContact()
    {
        $this->browse(function ($browser) {
            $browser->visit(new HomePage)
                    ->type('#homeContactForm input[name="name"]', 'Test name')
                    ->type('#homeContactForm input[name="email"]', 'test@sample.com')
                    ->type('#homeContactForm textarea[name="message"]', 'This is a sample message')
                    ->press('#homeContactForm button[type="submit"]')
                    ->waitForLocation((new HomePage)->url())
                    ->assertSee('Contact message has been sent');
        });
    }

    public function testComponents() 
    {
        $this->browse(function ($browser) {
            $browser->visit(new HomePage)
                    ->assertVisible('#main-footer')
                    ->assertVisible('#mainNav');
        });
    }

}
