<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\ContactUsPage;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Components\NavBar;
use Tests\Browser\Components\PublicFooter;

class ContactUsTest extends DuskTestCase
{
    /**
     * Assert base html elements are present.
     *
     * @return void
     */
    public function testHtml()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new ContactUsPage)

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
            $browser->visit(new ContactUsPage)
                // see the main phrase
                ->assertSee('Contact Us')

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
            $browser->visit(new ContactUsPage)
                    ->type('#ContactUsForm input[name="name"]', 'Test name')
                    ->type('#ContactUsForm input[name="email"]', 'test@sample.com')
                    ->type('#ContactUsForm textarea[name="message"]', 'This is a sample message')
                    ->press('#ContactUsForm button[type="submit"]')
                    /*->screenshot('error')*/
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
            $browser->visit(new ContactUsPage)
                    ->within(new NavBar, function ($browser) {
                        $browser->testPublicLinks($browser);
                    })
                    ->within(new PublicFooter, function ($browser) {
                        $browser->testContent($browser);
                    });
        });
    }
}
