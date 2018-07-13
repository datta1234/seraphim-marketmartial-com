<?php

namespace Tests\Browser\Components\TradeScreen;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class MarketTab extends BaseComponent
{
 
    public function __construct($id = null) {
        $this->tab_id = $id ? $id : '*';
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@market-tab-'.$this->tab_id;
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
            '@element' => '@market-tab-'.$this->tab_id,
        ];
    }

    
}
