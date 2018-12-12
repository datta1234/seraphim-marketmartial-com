<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use App\Models\TradeConfirmations\TradeConfirmation;
use Illuminate\Auth\Access\HandlesAuthorization;

class TradeConfirmationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Determine whether the user can dispute a TradeConfirmation.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\TradeConfirmations\TradeConfirmation $tradeConfirmation
     * @return mixed
     */
    public function dispute(User $user, TradeConfirmation $tradeConfirmation)
    {

        if($user->isViewer()) { 
            return false; 
        }
        return $user->isTrader();
    }

    /**
     * Determine whether the user can confirm a TradeConfirmation.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\TradeConfirmations\TradeConfirmation $tradeConfirmation
     * @return mixed
     */
    public function confirm(User $user, TradeConfirmation $tradeConfirmation)
    {

        if($user->isViewer()) { 
            return false; 
        }
        return $user->isTrader();
    }

    /**
     * Determine whether the user can continue with phaseTwo on a TradeConfirmation.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\TradeConfirmations\TradeConfirmation $tradeConfirmation
     * @return mixed
     */
    public function phaseTwo(User $user, TradeConfirmation $tradeConfirmation)
    {

        if($user->isViewer()) { 
            return false; 
        }
        return $user->isTrader();
    }

    /**
     * Determine whether the user can update the TradeConfirmation.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function update(User $user, TradeConfirmation $tradeConfirmation)
    {
        if($user->isViewer()) { 
            return false; 
        }
        return $user->isTrader();
    }

}
