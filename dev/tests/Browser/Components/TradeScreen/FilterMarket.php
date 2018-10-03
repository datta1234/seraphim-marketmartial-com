<?php

namespace Tests\Browser\Components\TradeScreen;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class FilterMarket extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@filter-markets-menu';
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

  

    public function open(Browser $browser)
    {
        $browser->press("@filter-button");
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@filter-button' => '#action-filter-market-button',
            '@pop-over' => '.popover'
        ];
    }
}
