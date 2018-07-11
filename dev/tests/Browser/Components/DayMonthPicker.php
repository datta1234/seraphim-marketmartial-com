<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DayMonthPicker extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '.day-month-picker';
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
                    '@month-list' => 'day-month-picker-month',
                    '@day-list' => 'day-month-picker-day'
                ];
    }

        /**
     * Select the given date.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @param  int  $month
     * @param  int  $day
     * @return void
     */
    public function selectDate(Browser $browser,$date)
    {        
        $browser->select('@month-list',$date->format('m'))
                ->select('@day-list',$date->format('d'));
    }
}
