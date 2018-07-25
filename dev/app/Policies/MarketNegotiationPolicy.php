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
        //
    }

     /**
     * Determine if the given post can be updated by the user.
     *
     * @param  \App\User  $user
     * @param  \App\UserMarket  $userMarket
     * @return bool
     */
    public function updateOnHold(User $user, MarketNegotiation $userMarketNegotiation)
    {
        return $request->user()->id === $marketNegotiation->user_id,$marketNegotiation->userMarkets->is_on_hold;
    }
}
