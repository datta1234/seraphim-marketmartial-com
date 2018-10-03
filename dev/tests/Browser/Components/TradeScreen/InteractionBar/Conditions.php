<?php

namespace Tests\Browser\Components\TradeScreen\InteractionBar;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class Conditions extends BaseComponent
{
    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        return '@ibar-apply-conditions';
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

        // initial state
        $browser->assertNotChecked('apply-a-condition')
            ->assertDontSee("Select a Condition");

        // open it
        $browser->click('@ibar-apply-a-condition')->screenshot('clicked')
            ->assertSee('Repeat all the way')
            ->assertSee('Propose (Private)')
            ->assertSee('OCO')
            ->assertSee('Subject')
            ->assertSee('FoK')
            ->assertSee('Meet in the Middle')
            ->assertSee('Buy/Sell at Best');

        $this->eachConditions($browser);

        // close it
        $browser->click('@ibar-apply-a-condition')->screenshot('clicked')
            ->assertDontSee('Repeat all the way')
            ->assertDontSee('Propose (Private)')
            ->assertDontSee('OCO')
            ->assertDontSee('Subject')
            ->assertDontSee('FoK')
            ->assertDontSee('Meet in the Middle')
            ->assertDontSee('Buy/Sell at Best');

    }


    private function eachConditions($browser) {

        // Repeat all the way
        $browser->press('Repeat all the way')
            ->waitFor('.ibar-condition-remove-label')
            ->within('.ibar-condition-remove-label', function($browser) {
                $browser->assertSee('Repeat all the way')
                        ->assertVisible('.remove')
                        ->click('.remove');
            })
            ->assertMissing('.ibar-condition-remove-label');
        
        // Propose (Private)
        $browser->press('Propose (Private)')
            ->waitFor('.ibar-condition-remove-label')
            ->within('.ibar-condition-remove-label', function($browser) {
                $browser->assertSee('Propose (Private)')
                        ->assertVisible('.remove')
                        ->click('.remove');
            })->assertMissing('.ibar-condition-remove-label');
        
        // OCO
        $browser->press('OCO')
            ->waitFor('.ibar-condition-remove-label')
            ->within('.ibar-condition-remove-label', function($browser) {
                $browser->assertSee('OCO')
                        ->assertVisible('.remove')
                        ->click('.remove');
            })->assertMissing('.ibar-condition-remove-label');
        
        // Subject
        $browser->press('Subject')
            ->waitFor('.ibar-condition-remove-label')
            ->within('.ibar-condition-remove-label', function($browser) {
                $browser->assertSee('Subject')
                        ->assertVisible('.remove')
                        ->click('.remove');
            })->assertMissing('.ibar-condition-remove-label');
        
        // FoK
        $browser->press('FoK')
            ->waitFor('.ibar-condition-remove-label')
            ->within('.ibar-condition-remove-label', function($browser) {
                $browser->assertSee('FoK')
                        ->assertVisible('.remove')
                        ->click('.remove');
            })->assertMissing('.ibar-condition-remove-label');
        
        // Meet in the Middle
        $browser->press('Meet in the Middle')
            ->waitFor('.ibar-condition-remove-label')
            ->within('.ibar-condition-remove-label', function($browser) {
                $browser->assertSee('Meet in the Middle')
                        ->assertVisible('.remove')
                        ->click('.remove');
            })->assertMissing('.ibar-condition-remove-label');
        
        // Buy/Sell at Best
        $browser->press('Buy/Sell at Best')
            ->waitFor('.ibar-condition-remove-label')
            ->within('.ibar-condition-remove-label', function($browser) {
                $browser->assertSee('Buy/Sell at Best')
                        ->assertVisible('.remove')
                        ->click('.remove');
            })->assertMissing('.ibar-condition-remove-label');


        // TODO: remove selected item on deselecting list item

        // vue data is correct
        $browser->assertVue('marketNegotiation.bid_qty', 500, '@ibar-market-negotiation-market')
                ->assertVue('marketNegotiation.offer_qty', 500, '@ibar-market-negotiation-market');

    }
    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '@ibar-apply-conditions',
        ];
    }
}
