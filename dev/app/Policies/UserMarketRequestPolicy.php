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
     * @param  \App\UserMarketRequest  $userMarketRequest
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
     * @param  \App\UserMarketRequest  $userMarketRequest
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
     * @param  \App\UserMarketRequest  $userMarketRequest
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
     * @param  \App\UserMarketRequest  $userMarketRequest
     * @return mixed
     */
    public function addQoute(User $user, UserMarketRequest $userMarketRequest)
    {
        return !$userMarketRequest->userMarkets()->activeQuotes()->whereHas('user',function($query) use ($user) {
            $query->where('organisation_id',$user->organisation_id);
        })->exists();
    }


}
