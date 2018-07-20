<?php

namespace App\Helpers\Broadcast;
use App\Models\UserManagement\Organisation;

class Channel
{
	public static function verifiedOrganisationsCached()
	{
    	$organisations = Organisation::getCached();
    	if($organisations->count() > 0)
    	{
    		return $organisations->where('verified',true);
    	}
    	return null;
	}

	
}