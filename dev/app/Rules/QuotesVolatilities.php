<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class QuotesVolatilities implements Rule
{
    private $groups;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userMarketRequest)
    {
        // get the 'choice' groups
        $this->groups = $userMarketRequest->userMarketRequestGroups()->chosen()->get();
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
        $vols = collect($value)->keyBy('group_id');
        foreach($this->groups as $group) {
            // if the choice vol set
            if(!isset($vols[$group->id])) {
                return false;
            }
            if( !isset($vols[$group->id]['value']) || empty($vols[$group->id]['value']) ) {
                return false;
            }
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
        return 'Must Provide choice volatility';
    }
}
