<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\UserManagement\Organisation;
use App\Models\UserManagement\Role;
use App\Models\StructureItems\MarketType;
use App\Models\UserManagement\User;
use Tests\Browser\Pages\RegisterPage;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\UserDetailsPage;
use Tests\Browser\Pages\TradeScreen;
use Tests\Browser\Components\NavBar;
use Tests\Browser\Components\PublicFooter;
use Tests\Browser\Components\TradeFooter;

class RegisterTest extends DuskTestCase
{
    protected static $dbSetup = false;
    protected static $role;
    protected static $organisation;
    protected static $market_type;
    protected static $user;

    /**
     * Called before each test method to create objects to test against.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        
        //Workaround to instantiate the db setup only once per class test
        if(!self::$dbSetup)
        {
            \Illuminate\Support\Facades\Artisan::call('migrate:fresh');
            
            //create a new user to login with, role and organisation needed for user creation
            self::$role = factory(Role::class)->create();
            self::$organisation = factory(Organisation::class)->create();
            self::$market_type = factory(MarketType::class)->create();
            self::$user = factory(User::class)->create([
                        'organisation_id' =>  self::$organisation->id,
                        'password'  =>  \Hash::make('samplepass')
                    ]);

            self::$dbSetup = true;
        }
    }

    /**
     * Called after each test method to destroy objects to test against.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
        });

        parent::tearDown();
    }
    
    /**
     * Assert base html elements are present.
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

    /**
     * Assert content sections are present present.
     *
     * @return void
     */
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

    /**
     * Asserting that cancelation of registration redirectect correctly.
     *
     * @return void
     */
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
     * @todo Implement UserDetailsPage - user 1ste time registration
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
                    ->select('#organisation_id', self::$organisation->id)
                    ->check('#market_types input[value="1"]')
                    ->select('role_id', self::$role->id)
                    ->type('#registerPageForm input[name="password"]', 'password')
                    ->type('#registerPageForm input[name="password_confirmation"]', 'password')
                    ->pause(100)
                    ->press('#registerPageForm button[type="submit"]')
                    ->waitForLocation((new UserDetailsPage)->url())
                    ->assertSeeLink('Logout');
        });
    }

    /**
     * Assert basic components are included.
     *
     * @return void
     */
    public function testComponents() 
    {
        $this->browse(function ($browser) {
            
            //Test components for Public User
            $browser->visit(new HomePage)
                    ->within(new NavBar, function ($browser) {
                        $browser->testPublicLinks($browser);
                    })
                    ->within(new PublicFooter, function ($browser) {
                        $browser->testContent($browser);
                    });

            //Test components for Authed User
            $browser->loginAs(User::find(1))
                    ->visit(new TradeScreen)
                    ->within(new TradeFooter, function ($browser) {
                        $browser->testContent($browser);
                    });
        });
    }
}
