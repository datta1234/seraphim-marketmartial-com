<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Organisation;
use Illuminate\Support\Facades\Cache;


class UuidGen implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $expiresAt = now()->addHours(24);
        Cache::put('usersMap',$this->generateUsersUuid(),$expiresAt);
        Cache::put('organisationsMap',$this->generateOrganisationUuid(),$expiresAt);
    }

    public function generateUsersUuid()
    {
        $users = User::all();
        $userUuid = [];

        foreach ($users as $user) 
        {
            $key = $this->gen_uuid();
            while(!in_array($key, $userUuid))
            {
                $userUuid[$user->id] = $key; 
            }
        }
        return $userUuid;
    }

    public function generateOrganisationUuid()
    {
        $organisations = Organisation::all();
        $orgUuid = [];

        foreach ($organisations as $organisation) 
        {
            $key = $this->gen_uuid();
            while(!in_array($key, $orgUuid))
            {
                 $orgUuid[$organisation->id] = $key; 
            }
        }
        return $orgUuid;
    }

    private function gen_uuid() 
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
}
