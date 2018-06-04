<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\ContactUsPage;
use Tests\Browser\Pages\HomePage;

class ContactUsTest extends DuskTestCase
{
    /**
     * A Dusk test example.
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
}
