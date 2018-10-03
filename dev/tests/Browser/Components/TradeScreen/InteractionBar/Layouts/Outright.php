<?php

namespace Tests\Browser\Components\TradeScreen\InteractionBar\Layouts;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Outright extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@ibar-negotiation-bar-outright';
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
            '@element' => '@ibar-negotiation-bar-outright',
        ];
    }
}
