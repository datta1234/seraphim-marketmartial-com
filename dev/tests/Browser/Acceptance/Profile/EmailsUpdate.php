<?php

namespace Tests\Browser\Acceptance\Profile;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\EmailsettingsPage;
use Tests\Browser\Pages\TradeSettingsPage;
use App\Models\UserManagement\DefaultLabel;
use Tests\Helper\FactoryHelper;

class EmailsUpdate extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        
        $organisation = factory(\App\Models\UserManagement\Organisation::class)->create(); 
        $user = factory(\App\Models\UserManagement\User::class)->create([
            'tc_accepted' => false,
            'organisation_id'=>$organisation->id
        ]);

        FactoryHelper::setUpDefaultEmails();
        $defaultLabels = DefaultLabel::all(); 
        $faker = \Faker\Factory::create();
        $userEmails = [];


        $this->browse(function (Browser $browser) use ($defaultLabels, $faker, $user){
            
            $browser->loginAs($user)
                    ->visit(new EmailsettingsPage)
                    ->assertSee('E-Mail Settings');

            //fill out the fields and add a new one

            for ($i=0; $i < count($defaultLabels) ; $i++)
            { 
                $email = $faker->safeEmail;
                $userEmails[] = ["title" => $defaultLabels[$i]->title, "email"=> $email];
                 $browser->assertSee($defaultLabels[$i]->title)
                          ->type('#email-'.$i.'-email',$email);
            }

            $browser->press("Add E-mail");
            $browser->whenAvailable('.modal', function ($modal) use($faker) {

                $title = $faker->name;
                $email = $faker->safeEmail;

                $userEmails[] = ["title" => $title, "email"=> $email];
                $modal->assertSee('Add Email')
                      ->type("#email-title",$title)
                      ->type("#email-email",$faker->safeEmail)
                      ->press('Save');
            });

            $browser->waitUntilMissing('.modal');
             $browser->press("#update-btn")
                     ->waitForLocation((new TradeSettingsPage)->url())
                     ->assertSee('Trading Account Settings');
            
            for ($i= count($userEmails) - 1 ; $i <= 0; $i--)
            {
                $browser->assertValue("input[name='email[".$i."][title]']",$userEmails[$i]['title'])
                        ->assertValue("input[name='email[".$i."][email]']",$userEmails[$i]['email']);
            }


        });
    
    }
}
