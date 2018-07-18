<?php

namespace App\Helpers\Misc;
use Illuminate\Support\Facades\Cache;
use App\Jobs\UuidGen;
use Carbon\Carbon;

class ResolveUuid
{
	public static function getUserUuid($id)
	{
		$users = Cache::get('usersMap');
		while(!is_array($users) || (is_array($users) && !array_key_exists($id, $users)))
		{
          UuidGen::dispatch();
		}
		return $users[$id];
	}

	public static function getOrganisationUuid($id)
	{
		$organisations = Cache::get('organisationsMap');
		while( !is_array($organisations) || (is_array($organisations) && !array_key_exists($id, $organisations)))
		{
          UuidGen::dispatch();
		}
		return $organisations[$id];
	}

	public static function getOrganisationsUuid()
	{
		$organisations = Cache::get('organisationsMap');
		while(!is_array($organisations))
		{
          UuidGen::dispatch();
		}
		return $organisations;
	}


}