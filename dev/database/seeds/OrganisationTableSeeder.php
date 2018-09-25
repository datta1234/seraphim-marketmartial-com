<?php

use Illuminate\Database\Seeder;

class OrganisationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orgs = 1;
        factory(App\Models\UserManagement\Organisation::class,8)->create()->each(function($organisation) use (&$orgs) {
               // dd($organisation);
    	        factory(App\Models\UserManagement\User::class, 4)->create([
					'organisation_id' =>  $organisation->id,
    	        ])
                ->each(function($user){
                    $markets = App\Models\StructureItems\Market::all();
                    foreach ($markets as $market) {
                        factory(App\Models\UserManagement\TradingAccount::class)->create([
                            'user_id' => $user->id,
                            'market_id' => $market->id
                        ]);
                    }
                });

                factory(App\Models\UserManagement\User::class)->create([
                    'email' =>  'org'.$orgs.'@example.net',
                    'organisation_id' =>  $organisation->id,
                ]);
                $orgs++;
        });
    }
}
