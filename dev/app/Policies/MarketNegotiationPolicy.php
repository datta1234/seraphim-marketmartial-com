<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Market\MarketNegotiation;

class MarketNegotiationPolicy
{
    use HandlesAuthorization;

        
    /**
     * Determine whether the user can create userMarketRequests.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

   /**
     * Determine whether the user can create userMarketRequests.
     *

     * @param  \App\Models\Market\MarketNegotiation $userMarket
     * @return bool
     * @return mixed
     */
    public function addTradeNegotiation(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can delete ( kill ) userMarketRequests.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\Market\MarketNegotiation $userMarket
     * @return bool
     */
    public function delete(User $user, MarketNegotiation $marketNegotiation)
    {
        // FoK
        if($marketNegotiation->isFoK()) {
            if($marketNegotiation->id !== $marketNegotiation->userMarket->current_market_negotiation_id) {
                return false;
            }
            return $user->organisation_id === $marketNegotiation->counterUser->organisation_id;
        }

        // Proposal || Meet In Middle
        if($marketNegotiation->isProposal() || $marketNegotiation->isMeetInMiddle()) {
            return $user->organisation_id === $marketNegotiation->counterUser->organisation_id;
        }

        return false;
    }

    /**
     * Determine whether the user can market negotiations.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\Market\MarketNegotiation $userMarket
     * @return bool
     */
    public function counter(User $user, MarketNegotiation $marketNegotiation)
    {
        return (
            $user->organisation_id == $marketNegotiation->counterUser->organisation_id &&
            !$marketNegotiation->marketNegotiationChildren()->exists()
        );
    }

}
