<?php
use App\Broadcasting\OrganisationChannel;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('organisation.{organisation}',function($user,$organisationUuid) {
    if($organisationUuid == "admin") {
        return $user->isAdmin();
    }
	return $user->organisation->uuid === $organisationUuid;
});
