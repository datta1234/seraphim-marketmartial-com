<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class TradeScreen extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/trade';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());

    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@toggle-theme'                 => '[data-toggle-theme]',
            '@active-markets-indicator'     => '#active-markets-indicator',
            '@action-filter-market-button'  => '#action-filter-market-button',
            '@action-bar-open-chat'         => '#action-bar-open-chat',  
        ];
    }
}
