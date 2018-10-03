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
        $faker = \Faker\Factory::create();
        $markets = Market::all();

        $this->browse(function (Browser $browser) use ($user,$markets, $faker){
                $browser->loginAs($user)
                    ->visit(new TradeSettingsPage)
                    ->assertSee('Trading Account Settings');

        for ($i=0; $i < count($markets); $i++) 
        { 
            $browser->assertSee($markets[$i]->title)
                    ->type('trading_accounts['.$i.'][safex_number]',$faker->bankAccountNumber)
                    ->type('trading_accounts['.$i.'][sub_account]',$faker->bankAccountNumber);

        }

       $browser->press('@submit')
                ->assertPathIs('/interest-settings')
                ->assertSee('Tell Us More About Yourself');

        });
    }


}
