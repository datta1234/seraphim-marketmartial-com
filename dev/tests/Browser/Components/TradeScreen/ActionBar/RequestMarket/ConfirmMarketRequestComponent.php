<?php

namespace Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class ConfirmMarketRequestComponent extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@confirm-market-request';
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

    public function assertDetails(Browser $browser,$selectedMarket,$selectedTradeStructure,$selectedExpiryDates,$groups)
    {        
        $browser->assertSee($selectedMarket);
        $browser->assertSee($selectedTradeStructure);

        foreach ($selectedExpiryDates as $selectedExpiryDate) 
        {
             $browser->assertSee($selectedExpiryDate->format('My'));
        }

        foreach ($groups as $groupItems) 
        {
            foreach ($groupItems as $item => $value) 
            {
                 $browser->assertSee($value);
            }
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
            '@element' => '@confirm-market-request',
        ];
    }
}
