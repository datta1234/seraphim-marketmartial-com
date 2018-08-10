<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Market\UserMarket;

class UserMarketPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given usermarket can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserMarket  $userMarket
     * @return bool
     */
    public function update(User $user, UserMarket $userMarket)
    {
        return $user->orgnisation_id === $userMarket->user->orgnisation_id;
    }    

     /**
     * Determine if the given usermarket can be placed on hold the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserMarket  $userMarket
     * @return bool
     */
    public function placeOnHold(User $user, UserMarket $userMarket)
    {
        return $userMarket->userMarketRequest->user->organisation_id == $user->organisation_id;
    }


    public function updateNegotiation(User $user, UserMarket $userMarket)
    {
        return $userMarket->marketNegotiations()->where('user_id',$user->id)->exists();
    }

    
    /**
     * Determine if the given usermarket can be deleted by the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserMarket  $userMarket
     * @return bool
     */
    public function delete(User $user, UserMarket $userMarket)
    {
        return $user->orgnisation_id === $userMarket->user->orgnisation_id;
    }
}
