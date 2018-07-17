<?php

namespace Tests\Browser\Components\TradeScreen\ActionBar\RequestMarket;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;
use Carbon\Carbon;

class ExpirySelectionComponent extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@expiry-selection';
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

    public function selectDate($browser,$date)
    {
            $page = $this->getdatePage($date);

            if($page == 1)
            {
                $this->pressDate($browser,$date);
            }else
            {
               $browser->with('@pagination', function ($pagination) use ($browser,$page,$date){

                    $browser->assertSee($pagination)
                               ->press($page);

                    $this->pressDate($browser,$date);

                });
            }
         
    }  

    private function pressDate($browser,$date)
    {
         $page->assertSee($date->format('MY'))
              ->press($date->format('MY'));
    }

    private function getDatePage($date)
    {
        $datesPerPage = 12;
        $dates = \App\Models\StructureItems\SafexExpirationDate::where('expiration_date','>',Carbon::now())->get();

        foreach ($dates as $key => $value) 
        {
           if($date->id == $value->id)
           {
                return  ceil(($key + 1)/ $datesPerPage);
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
            '@element' => '@expiry-selection',
            '@pagination' => '.pagination'
        ];
    }
}
