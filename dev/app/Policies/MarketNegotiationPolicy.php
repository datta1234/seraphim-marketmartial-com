<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Market\MarketNegotiation;

class MarketNegotiationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        
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
         * Determine if the given usermarket accept the user.
         *
         * @param  \App\Models\UserManagement\User  $user
         * @param  \App\Models\Market\MarketNegotiation $userMarket
         * @return bool
         */
        public function spin(User $user, MarketNegotiation $marketNegotiation)
        {
            return $marketNegotiation;
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
            return $user->orgnisation_id === $marketNegotiation->marketNegotiationParent->user->orgnisation_id;
        }

    }

}
