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
use Tests\Browser\Components\NavBar;
use Tests\Browser\Components\PublicFooter;
use Tests\Browser\Components\TradeFooter;

use Illuminate\Contracts\Console\Kernel;

class HomePageTest extends DuskTestCase
{
    protected static $dbSetup = false;
    protected static $role;
    protected static $organisation;
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
            $browser->visit(new HomePage)

                // html markup
                ->assertTitle('Market Martial');
        });
    }

    /**
     * Asserting basic system login.
     *
     * @return void
     */
    public function testLogin()
    {
        $this->browse(function ($browser) {
            
            $browser->visit(new HomePage)
                    ->type('#homePageLoginForm input[name="email"]', self::$user->email)
                    ->type('#homePageLoginForm input[name="password"]', 'samplepass')
                    ->press('#homePageLoginForm button[type="submit"]')
                    ->waitForLocation((new TradeScreen)->url())
                    ->assertSeeLink('Logout');
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

    /**
     * Asserting contact form submission and system response.
     *
     * @return void
     */
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
                    ->visit(new HomePage)
                    ->within(new NavBar, function ($browser) {
                        $browser->testAuthLinks($browser);
                    })
                    ->within(new PublicFooter, function ($browser) {
                        $browser->testContent($browser);
                    });
        });
    }
}
