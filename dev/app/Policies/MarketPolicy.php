<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use App\Models\StructureItems\Market;

use Illuminate\Auth\Access\HandlesAuthorization;

class MarketPolicy
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
     * Determine whether the user can add a new Market Request.
     *
     * @param  \App\Models\UserManagement\User  $user
     * @param  \App\UserMarketRequest  $market
     * @return mixed
     */
    public function addMarketReqeust(User $user, Market $market)
    {
        return $user->isTrader();
    }

}
