<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\AboutPage;

class AboutTest extends DuskTestCase
{
    /**
     * A Dusk test example.
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

    public function testComponents() 
    {
        $this->browse(function ($browser) {
            $browser->visit(new AboutPage)
                    ->assertVisible('#main-footer')
                    ->assertVisible('#mainNav');
        });
    }

}
