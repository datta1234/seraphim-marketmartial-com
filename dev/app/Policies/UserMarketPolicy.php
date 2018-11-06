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


    /**
     * Determine if the given usermarket accept the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserMarket  $userMarket
     * @return bool
     */
    public function accept(User $user, UserMarket $userMarket)
    {
        return $userMarket->userMarketRequest->user->organisation_id == $user->organisation_id;
    }


    public function updateNegotiation(User $user, UserMarket $userMarket)
    {
        return $userMarket->marketNegotiations()->where(function($query) use ($user)
        {
            $query->whereHas('user',function($query) use ($user){
                $query->where('organisation_id', $user->organisation_id);
            });
        })->exists() && !$userMarket->userMarketRequest->chosenUserMarket()->exists();
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
        return $user->organisation_id === $userMarket->user->organisation_id 
            && !$userMarket->userMarketRequest->chosenUserMarket()->exists();
    }

    public function addNegotiation(User $user, UserMarket $userMarket)
    {
        $current_org_id = $user->organisation_id;

        // Cant Negotiate With Self
        // if($userMarket->lastNegotiation->user->organisation_id == $current_org_id) {
        //     return false;
        // }

        // Cant respond to negotiation if FoK
        if($userMarket->lastNegotiation->isFoK()) {
            // only if its killed
            return $userMarket->lastNegotiation->is_killed == true;
        }

        // if the last one was an Repeat ATW
        if($userMarket->lastNegotiation->isRepeatATW()) {
            return false;
        }
        
        return $userMarket->userMarketRequest->isAcceptedState($current_org_id) && 
            in_array(
                $userMarket->userMarketRequest->getStatus($current_org_id), 
                ["negotiation-pending", "negotiation-open", "trade-negotiation-open","trade-negotiation-balance"]
            );
    }

    public function spinNegotiation(User $user, UserMarket $userMarket)
    {
        $current_org_id = $user->organisation_id;
        return $userMarket->userMarketRequest->isAcceptedState($current_org_id);
    }
    
}