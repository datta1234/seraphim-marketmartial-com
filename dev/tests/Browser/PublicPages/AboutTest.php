<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\AboutPage;
use Tests\Browser\Components\NavBar;
use Tests\Browser\Components\PublicFooter;

class AboutTest extends DuskTestCase
{
    /**
     * Assert base html elements are present.
     *
     * @return void
     */
    public function testHtml()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new AboutPage)

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
            $browser->visit(new AboutPage)
                // see the main phrase
                ->assertSee('About us')

                // title sections
                ->assertSee('The Next Generation of Derivatives Trading')
                ->assertSee('Why Us?')

                // menu tests
                ->assertSeeLink('About Us')
                ->assertSeeLink('Contact Us')
                ->assertSeeLink('Sign up now');
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
            $browser->visit(new AboutPage)
                    ->within(new NavBar, function ($browser) {
                        $browser->testPublicLinks($browser);
                    })
                    ->within(new PublicFooter, function ($browser) {
                        $browser->testContent($browser);
                    });
        });
    }

}
