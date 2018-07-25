<?php

namespace App\Policies;

use App\Models\UserManagement\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Market\UserMarket;

class UserMarketPolicy
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
     * @param  \App\Post  $post
     * @return bool
     */
    public function delete(User $user, UserMarket $userMarket)
    {
        return $user->id === $userMarket->user_id;
    }
}
