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
         * @param  \App\User  $user
         * @param  \App\UserMarket  $userMarket
         * @return bool
         */
        public function spin(User $user, MarketNegotiation $marketNegotiation)
        {
            return $marketNegotiation;
        }

    }

}
