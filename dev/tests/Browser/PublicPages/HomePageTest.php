<?php

namespace Tests\Browser\PublicPages;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Tests\Browser\Pages\HomePage;

class HomePageTest extends DuskTestCase
{
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
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage);

            // Login box
            $this->assertTrue($browser->element('input[type="hidden"][name="_token"]'));
            $this->assertTrue($browser->element('input[type="email"][name="email"]'));
            $this->assertTrue($browser->element('input[type="password"][name="password"]'));
            $this->assertTrue($browser->element('input[type="submit"]'));
            
            $browser->assertSeeLink('Forgot Password');
        });
    }

    public function testContent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage)

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

}
