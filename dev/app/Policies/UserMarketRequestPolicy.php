<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use App\Models\MarketRequest\UserMarketRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserMarketRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the userMarketRequest.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function view(User $user, UserMarketRequest $userMarketRequest)
    {
        //
    }

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
     * Determine whether the user can deactivate the userMarketRequest.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function deactivate(User $user, UserMarketRequest $userMarketRequest)
    {
        // only admins can pull a market request
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the userMarketRequest.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function delete(User $user, UserMarketRequest $userMarketRequest)
    {
        //
    }

    /**
     * Determine whether the user can update the userMarketRequest.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function update(User $user, UserMarketRequest $userMarketRequest)
    {
        //
    }

    /**
     * Determine whether the user can delete the userMarketRequest.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function addQoute(User $user, UserMarketRequest $userMarketRequest)
    {
        if(!$user->isTrader()) { 
            return false; 
        }
        return !$userMarketRequest->userMarkets()->activeQuotes()->whereHas('user',function($query) use ($user) {
            $query->where('organisation_id',$user->organisation_id);
        })->exists();
    }

    /**
     * Determine whether the user remove notification for actions that need to be taken.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function actionTaken (User $user, UserMarketRequest $userMarketRequest)
    {
        return $user->isTrader();
    }

    /**
     * Determine whether the user is allowed a subscription to be notified of a cleared userMarketRequest.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\Models\MarketRequest\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function ToggleAlertCleared (User $user, UserMarketRequest $userMarketRequest)
    {
        return $user->isTrader();
    }
}
