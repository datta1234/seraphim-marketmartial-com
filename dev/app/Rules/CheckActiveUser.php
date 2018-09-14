<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\UserManagement\User;

class CheckActiveUser implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = User::where('email', $value)->first();
        
        if($user) {
            return $user->verified ? $user->active : true;   
        }
        
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'User account has been Deactivated.';
    }
}
