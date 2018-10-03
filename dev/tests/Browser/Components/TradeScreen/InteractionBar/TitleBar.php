<?php

namespace Tests\Browser\Components\TradeScreen\InteractionBar;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class TitleBar extends BaseComponent
{
    public function __construct($type = null, &$data) {
        $this->type = $type;
        $this->marketData = $data;
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@ibar-user-market-title';
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

        switch($this->type) {
            // Outright
            case 'Outright':
                $browser->assertSee(
                    $this->marketData['market']->title.' '.
                    $this->marketData['user_market_request_formatted']['trade_items']['default']['Expiration Date'].' '.
                    $this->marketData['user_market_request_formatted']['trade_items']['default']['Strike']
                )
                ->assertSee($this->marketData['user_market_request']->updated_at->format("H:i"));
            break;
        }
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '@ibar-user-market-title',
        ];
    }
}
