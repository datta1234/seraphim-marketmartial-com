<?php

namespace Tests\Browser\Components\TradeScreen;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class UserHeader extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@user-header';
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

    public function checkTime(Browser $browser, $time)
    {
        $browser->waitForText($time->format("H:i").' SA');
        $browser->assertSee($time->format("H:i").' SA');

    }

    public function checkWelcomeMessage(Browser $browser, $user)
    {   
        if($user->organisation)
        {
           $browser->assertSee("Welcome {$user->full_name} ({$user->organisation->title})"); 
        }else
        {
           $browser->assertSee("Welcome {$user->full_name}"); 
        }
    }

    public function checkRebates(Browser $browser, $user)
    {
        $total = rtrim(number_format($user->userTotalRebate(),2,'.',' '),"0");
        $total = rtrim($total,".");
        $browser->assertSee("Rebates: R{$total}");
    }


    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '@user-header',
            '@current-time'    =>  '.sub-nav .current-time',
            '@user-details'     =>  '.sub-nav .user-details',
        ];
    }
}
