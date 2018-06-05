<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class PublicFooter extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '#main-footer';
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
            '@footer-links' => '.footer-links-block',
        ];
    }

    /**
     * Assert that the footer links are present.
     *
     * @return void
     */
    public function testContent(Browser $browser)
    {
        $browser->assertSeeLink('T&Cs')
                ->assertSeeLink('Privacy Policy')
                ->assertSeeLink('Conflict of interest')
                ->assertSeeLink('Compliance overview')
                ->assertSeeLink('FSP specific documentation');
    }
}
