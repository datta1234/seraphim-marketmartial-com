<?php

namespace App\Helpers\Misc;
use Illuminate\Support\Facades\Cache;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Organisation;
use Carbon\Carbon;
use App\Events\UUIDUpdated;

class ResolveUuid
{
	public static function getUserUuid($id)
	{
		$users = Cache::get('usersMap');
		if(!is_array($users) || (is_array($users) && !array_key_exists($id, $users)))
		{
     		$users = self::generateUsersUuid();
		}
		return $users[$id];
	}

	public static function getOrganisationUuid($id)
	{
		$organisations = Cache::get('organisationsMap');
		if( !is_array($organisations) || (is_array($organisations) && !array_key_exists($id, $organisations)))
		{
        	$organisations = self::generateOrganisationUuid(); 
		}

		return $organisations[$id];
	}

    public static function generateOrganisationUuid()
    {
        $old_uuids = Cache::get('organisationsMap');
        $organisations = Organisation::all();
        $orgUuid = [];

        foreach ($organisations as $organisation) 
        {
            do {
                $key = self::genUuid();
            } while(in_array($key, $orgUuid));
            $orgUuid[$organisation->id] = $key; 
        }

		$expiresAt = now()->addHours(24);
    	Cache::put('organisationsMap',$orgUuid,$expiresAt);

        foreach ($organisations as $organisation) 
        {
            // let the clients know their UUID has been updated
            if(isset($old_uuids[$organisation->id])) {
                event(new UUIDUpdated($organisation, $old_uuids[$organisation->id]));
            }
        }

        return $orgUuid;
    }

    public static function generateUsersUuid()
    {
        $users = User::all();
        $userUuid = [];

        foreach ($users as $user) 
        {
            do {
                $key = self::genUuid();
            } while(in_array($key, $userUuid));
            $userUuid[$user->id] = $key; 
        }

		$expiresAt = now()->addHours(24);
    	Cache::put('usersMap',$userUuid,$expiresAt);
        return $userUuid;
    }

	public static function genUuid() 
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