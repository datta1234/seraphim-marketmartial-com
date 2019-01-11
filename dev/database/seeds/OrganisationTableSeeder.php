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
        
        for($i = 1; $i < 8; $i++) {
            $organisation = factory(App\Models\UserManagement\Organisation::class)->create([
                "title" => "Organisation ".$i
            ]);
            $user = factory(App\Models\UserManagement\User::class)->create([
                // 'verified'  =>  true,
                // 'active'  =>  true,
                'email' =>  'org'.$i.'@example.net',
                'organisation_id' =>  $organisation->id,
            ]);
            $markets = App\Models\StructureItems\Market::all();
            foreach ($markets as $market) {
                factory(App\Models\UserManagement\TradingAccount::class)->create([
                    'user_id' => $user->id,
                    'market_id' => $market->id
                ]);
            }
        }


     //            factory(App\Models\UserManagement\User::class)->create([
     //                // 'verified'  =>  true,
     //                // 'active'  =>  true,
     //                'email' =>  'org'.$orgs.'@example.net',
     //                'organisation_id' =>  $organisation->id,
     //            ])->each(function($user){
     //                $markets = App\Models\StructureItems\Market::all();
     //                foreach ($markets as $market) {
     //                    factory(App\Models\UserManagement\TradingAccount::class)->create([
     //                        'user_id' => $user->id,
     //                        'market_id' => $market->id
     //                    ]);
     //                }
     //            });
     //            $orgs++;
     //    });
    }
}
