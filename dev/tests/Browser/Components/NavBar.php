<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class NavBar extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '#mainNav';
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@nav-list' => '.nav',
        ];
    }

    /**
     * Assert that the Nav public links are present.
     *
     * @return void
     */
    public function testPublicLinks(Browser $browser)
    {
        $browser->assertSeeIn('@nav-list', 'About Us')
                ->assertSeeIn('@nav-list', 'Contact Us')
                ->assertSeeIn('@nav-list', 'Sign up now')
                ->assertDontSeeIn('@nav-list', 'Trade')
                ->assertDontSeeIn('@nav-list', 'Stats')
                ->assertDontSeeIn('@nav-list', 'Previous day')
                ->assertDontSeeIn('@nav-list', 'More')
                ->assertDontSeeIn('@nav-list', 'Logout');
    }

    /**
     * Assert that the Nav Authed only links are present.
     *
     * @return void
     */
    public function testAuthLinks(Browser $browser)
    {
        $browser->assertSeeIn('@nav-list', 'Trade')
                ->assertSeeIn('@nav-list', 'Stats')
                ->assertSeeIn('@nav-list', 'Previous day')
                ->assertSeeIn('@nav-list', 'More')
                ->assertSeeIn('@nav-list', 'Logout')
                ->assertDontSeeIn('@nav-list', 'About Us')
                ->assertDontSeeIn('@nav-list', 'Contact Us')
                ->assertDontSeeIn('@nav-list', 'Sign up now');
    }
}
