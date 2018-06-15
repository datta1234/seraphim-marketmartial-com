<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class MyProfilePage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/my-profile';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@fullname' => 'input[name=full_name]',
            '@cellphone' => 'input[name=cell_phone]',
            '@workphone' => 'input[name=work_phone]',
            '@email' =>  'input[name=email]',
            '@organisation' => 'select[name="organisation_id"]',
            '@submit' => 'input[type=submit]'
        ];
    }
}
