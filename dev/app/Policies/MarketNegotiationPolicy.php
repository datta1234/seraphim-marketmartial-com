<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Market\MarketNegotiation;
use Illuminate\Support\Facades\DB;

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
    public function addTradeNegotiation(User $user, MarketNegotiation $marketNegotiation)
    {
        if(!$user->isTrader()) { 
            return false; 
        }
        if($marketNegotiation->isTraded()) {
            return false;
        }
        if($marketNegotiation->isTrading()) {
            // only involved
            return $marketNegotiation->lastTradeNegotiation->isOrganisationInvolved($user->organisation_id);
        }
        return $user->isTrader();
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
        if($user->isAdmin()) {
            return true;
        }
        if(!$user->isTrader()) { 
            return false; 
        }
        
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
        if(!$user->isTrader()) { 
            return false; 
        }
        return (
            $user->organisation_id == $marketNegotiation->counterUser->organisation_id &&
            !$marketNegotiation->marketNegotiationChildren()->exists()
        );
    }

    public function amend(User $user, MarketNegotiation $marketNegotiation)
    {
        if(!$user->isTrader()) { 
            return false; 
        }
        $userMarket = $marketNegotiation->userMarket;
        $current_org_id = $user->organisation_id;
        $lastNegotiation = $userMarket->lastNegotiation;

        // not valid user
        if($current_org_id != $lastNegotiation->user->organisation_id) {
            return false;
        }
        
        // Cant respond to negotiation if FoK
        if($lastNegotiation->id != $marketNegotiation->id) {
            // only if its killed
            return false;
        }

        // cant amend if its traded or spun
        if($lastNegotiation->isTraded() || $lastNegotiation->isSpun()) {
            return false;
        }

        return $userMarket->userMarketRequest->isAcceptedState($current_org_id) && 
            in_array(
                $userMarket->userMarketRequest->getStatus($current_org_id), 
                ["negotiation-pending", "negotiation-open", "trade-negotiation-open","trade-negotiation-balance"]
            );
    }


    /**
     * Determine whether the user can improve the market negotiation
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\Market\MarketNegotiation $userMarket
     * @return bool
     */
    public function improveBest(User $user, MarketNegotiation $marketNegotiation)
    {
        if(!$user->isTrader()) { 
            return false; 
        }
        $user_market = $marketNegotiation->userMarket;
        $current_best = $user_market->lastNegotiation;
        return (
            $marketNegotiation->isTradeAtBest() &&
            $current_best->isTradeAtBestOpen() &&
            !$current_best->isTrading() &&
            !!$user_market
        );
    }


    public function refreshLevels(User $user)
    {
        return $user->isTrader();
    }

    public function alterTradeAtBestTimer(User $user, MarketNegotiation $marketNegotiation)
    {
        if($user->isAdmin()) {
            // Find current Job
            $job = DB::table('jobs')->where("id",$marketNegotiation->job_id)->count();
            // If there is no current job queued or the timeout still has not reached the lock timeout condition
            if($job < 1 || $marketNegotiation->timeoutLocked()) {
                return false;
            }

            return true;
        } 
        
        return false; 
    }
}
