<?php

namespace Tests\Browser\Acceptance\Profile;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\TradeSettingsPage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Helper\FactoryHelper;
use App\Models\StructureItems\Market;

class TradeAccountSettingsUpdate extends DuskTestCase
{
    use DatabaseMigrations;

   /**
    * @test
    * @group FirstTimeLogin
    * @group AccountUpdate
    */
    public function FirstAccountUpdateProfile()
    {

        $organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $user = factory(\App\Models\UserManagement\User::class)->create([
            'tc_accepted' => false,
            'organisation_id'=>$organisation->id
        ]);

        FactoryHelper::setUpMarkets();
        $markets = Market::all();

        $this->browse(function (Browser $browser) use($user,$markets){
                $browser->loginAs($user)
                    ->visit(new TradeSettingsPage)
                    ->assertSee('Trading Account Settings');

            // fields are based of the markets in the database
            $markets->each(function($market) use($browser){

                $browser->assertSee($market->title);

            });

        });
    }


}
