<?php

namespace Tests\Browser\Components\TradeScreen;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class InteractionBar extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@interaction-bar';
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
            '@element' => '@interaction-bar',
            '@send-button' => '@ibar-action-send',
            '@send-disabled-button' => 'button[type="button"][disabled].ibar-action-send',
            '@nocares-button' => '@ibar-action-nocares',
            '@amend-button' => 'button[type="button"].ibar-action-amend',
            '@amend-disabled-button' => 'button[type="button"][disabled].ibar-action-amend',
            '@repeat-button' => '@ibar-action-repeat',
            '@pull-button' =>'@ibar-action-pull' ,
        ];
    }
}
