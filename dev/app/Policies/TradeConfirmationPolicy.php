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
        // Status 1 or 3 = send user 2 or 5 received user anything else is not allowed 
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [3])) {
            // test sender org
            if($tradeConfirmation->sendUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [2,5])) {
            // test receiver org
            if($tradeConfirmation->recievingUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        return false;
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
        // Status 3 = send user 2 or 5 received user anything else is not allowed 
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [3])) {
            // test sender org
            if($tradeConfirmation->sendUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [2,5])) {
            // test receiver orgs
            if($tradeConfirmation->recievingUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        return false;
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
        // Status 1 or 3 = send user 2 or 5 received user anything else is not allowed 
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [1,3])) {
            // test sender org
            if($tradeConfirmation->sendUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [2,5])) {
            // test receiver org
            if($tradeConfirmation->recievingUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        return false;
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
        // Status 1 or 3 = send user 2 or 5 received user anything else is not allowed 
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [1,3])) {
            // test sender org
            if($tradeConfirmation->sendUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        if( in_array($tradeConfirmation->trade_confirmation_status_id, [2,5])) {
            // test receiver org
            if($tradeConfirmation->recievingUser->organisation_id == $user->organisation_id) {
                return $user->isTrader();
            }
        }
        return false;
    }

}
