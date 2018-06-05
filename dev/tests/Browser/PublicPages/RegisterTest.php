<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\Role;
use App\Models\StructureItems\MarketType;
use Tests\Browser\Pages\RegisterPage;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\UserDetailsPage;

class RegisterTest extends DuskTestCase
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
            $browser->visit(new RegisterPage)

                // html markup
                ->assertTitle('Market Martial');
        });
    }

    public function testContent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new RegisterPage)
                // see the main phrase
                ->assertSee('New Member Enquiry')

                // important sections
                ->assertSee('Why do I need to wait for verification?')
                
                // menu tests
                ->assertSeeLink('About Us')
                ->assertSeeLink('Contact Us')
                ->assertSeeLink('Sign up now');
        });
    }

    public function testCancel()
    {
        $this->browse(function ($browser) {
            $browser->visit(new RegisterPage)
                    ->clickLink('Cancel')
                    ->waitForLocation((new HomePage)->url())
                    ->assertSeeLink('Sign up now');
        });
    }
    /**
     * A User registration test that test if the user logs in after registration.
     *
     * @todo Change the redirection url to the registration
     *
     * @return void
     */
    public function testRegistration()
    {
        $this->browse(function ($browser) {
            $role = factory(Role::class)->create();
            $organisation = factory(Organisation::class)->create([
                'verified' => 1,
            ]);
            $market_type = factory(MarketType::class)->create();

            $browser->visit(new RegisterPage)
                    ->type('#registerPageForm input[name="email"]', 'dude@sample.com')
                    ->type('#registerPageForm input[name="full_name"]', 'The Dude')
                    ->type('#registerPageForm input[name="cell_phone"]', '0825478896')
                    ->select('#organisation_id', $organisation->id)
                    ->check('#market_types input[value="1"]')
                    ->select('role_id', $role->id)
                    ->type('#registerPageForm input[name="password"]', 'password')
                    ->type('#registerPageForm input[name="password_confirmation"]', 'password')
                    ->pause(100)
                    ->press('#registerPageForm button[type="submit"]')
                    ->waitForLocation((new UserDetailsPage)->url())
                    ->assertSeeLink('Logout');
        });
    }
}
