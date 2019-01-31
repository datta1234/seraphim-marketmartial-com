<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use App\Models\Trade\TradeNegotiation;
use Illuminate\Auth\Access\HandlesAuthorization;

class TradeNegotiationPolicy
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
     * Determine whether the user can apply noFurtherCares a TradeNegotiation.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\TradeConfirmations\TradeConfirmation $tradeNegotiation
     * @return mixed
     */
    public function applyNoFurtherCares(User $user, TradeNegotiation $tradeNegotiation)
    {
        return $user->isTrader();
    }
}
